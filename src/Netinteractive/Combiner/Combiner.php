<?php namespace Netinteractive\Combiner;

use Netinteractive\Utils\Utils;
use Opis\Closure\SerializableClosure;

class Combiner
{
    public static $OUTPUT_MODE = 0777;

    /**
     * Lista wymaganych kluczy pliku konfiguracyjnego
     * @var array
     */
    protected $keysToCheck = array(
        'savePath',
        'handler',
        'paths'
    );

    protected $skin;
    protected $type;
    protected $mode;

    #Handler do wygenerownego pliku (minify albo obfuscate)
    protected $handler;

    #pliki do zalodownia
    protected $paths=array();

    #sciezka do ktorej trzeba zapisac wygenerowany plik
    protected $savePath;

    /**
     * @param $skin
     * @param $type
     * @param $mode
     */
    public function load($skin, $mode, $type)
    {
        $this->setSkin($skin);
        $this->setMode($mode);
        $this->setType($type);

        $config =  \Config("packages.netinteractive.combiner.config.$skin.$type.$mode");

        if ($config){
            $this->loadConfig($config);
        }


        return $this;
    }

    /**
     * Generuje html dla plikow, ktore nie wchodza do mergea
     * @param string $tag
     * @return string
     */
    public function html($tag=null)
    {
        $html = '';
        $fileList = $this->buildFileList(false, $tag);

        foreach ($fileList AS $filePath){
            if ($this->getType() == 'css'){
                $html .= '<link href="'.$filePath.'" rel="stylesheet">'."\n";
            }else{
                $html .= '<script src="'.$filePath.'"></script>'."\n";
            }

        }

        return $html;
    }

    /**
     * Laczy pliki w jeden
     * @return $this
     */
    public function combine()
    {

        if( \Config::get('app.debug') || !file_exists($this->getSavePath())){
            #budowanie tresci
            $content = $this->buildOutputContent($this->buildFileList());

            #zapis
            $this->saveOutputFile($content);
        }
        
        return $this->makeUrl();
    }

    /**
     * Builds file list
     * @param boolean $combine - true, zwraca pliki ktore powinne byc zmergowane. false, pliki do html
     * @param string $tag - jesli podany, zostana zwrocone pliki otagowane wskazanym tagiem
     * @return array
     */
    public function buildFileList($combine=true, $tag=null)
    {
        $filesList = array(
            'combine' => array(),
            'html' => array()
        );

        foreach($this->getPaths() AS $path){
            $currentPath = null;
            $after = null;
            $type = 'combine';

            #sprawdzamy czy path mamy jako bezposrednia sciezke, czy tez tablice konfiguracyjna
            if (is_array($path)){
                $currentPath = $path['path'];

                #combine
                if (array_key_exists('combine', $path) && $path['combine'] == false){
                    $type = 'html';
                }

                #lista plikow wymaganych do merga przed przetwarzanym plikiem
                if (array_key_exists('after', $path)){
                    $after =  $path['after'];
                }

                #pliki dla wersji mobile
                if (array_key_exists('mobile', $path) && $path['mobile'] == true){
                    if (!isMobile()){
                        break;
                    }
                }

                #jesli mamy podane jezyki, dla ktorych plik ma byc mergowany
                if (array_key_exists('langs', $path)){
                    $langs = $path['langs'];
                    if (!is_array($langs)){
                        $langs = array($langs);
                    }

                    foreach($langs AS $langShort){
                        if ($langShort == \App::getLocale()){
                            $currentPath = $path['path'];
                            break;
                        }
                    }
                }

                #tagi
                if ($type == 'html'){
                    if ($tag){
                        if (!array_key_exists('tag', $path)  || $path['tag'] != $tag){
                            $currentPath = null;
                        }
                    }
                    else{
                        if (array_key_exists('tag', $path)){
                            $currentPath = null;
                        }
                    }
                }
            }
            else{
                $currentPath = $path;
            }
            
            #mozemy nie miec sciezki jesli jest do plik dla konkretnej wersji jezykowej, a nie jest to wersja
            #aktualnie uzywana przez aplikacje
            if ($currentPath){
                if ( is_dir($currentPath) ){
                    $dirFiles = \Utils::scanDir($currentPath, array('f', 'd'), true);

                    foreach ($dirFiles AS $filePath){
                        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                        if ($ext == $this->getType()){
                            $key = $this->fileKey($filePath);

                            if (!isSet($filesList[$type][$key])){
                                $filesList[$type][$key] = array();
                            }
                            $filesList[$type][$key]['name'] = $filePath;

                            #dodajemy informacje, ze plik ma sie zaldowac po innym pliku
                            if ($after){
                                $filesList[$type][$key]['after'] = $after;
                                $after = null;
                            }
                        }
                    }
                }
                else{
                    $key = $this->fileKey($currentPath);
                    if (!isSet($filesList[$type][$key])){
                        $filesList[$type][$key] = array();
                    }

                    $filesList[$type][$key]['name'] = $currentPath;
                    #dodajemy informacje, ze plik ma sie zaldowac po innym pliku
                    if ($after){
                        $filesList[$type][$key]['after'] = $after;
                    }
                }
            }

        }

        $filesList = $this->clearFileList($filesList);

        #manipulujemy kolejnoscia plikow na liscie, tak aby pliki, ktore wymagaja innych, nie zostaly dodane do combinera lub layoutu za wczesnie
        $filesList['combine'] = $this->reorderList($filesList['combine']);
        $filesList['html'] = $this->reorderList($filesList['html']);

        #unique
        if ($combine == true){
            return $filesList['combine'];
        }

        return  $filesList['html'];
    }

    /**
     * Reorders files in list
     * @param array $fileList
     * @return array
     */
    protected function reorderList(array $fileList)
    {
        $resultFileList = array();
        foreach ($fileList AS $key=>$fileData){
            if ( array_key_exists('after', $fileData)  && array_key_exists($fileData['after'], $fileList) ){
                if (!isSet($fileList[$fileData['after']]['include_after'])){
                    $fileList[$fileData['after']]['include'] = array();
                }
                $fileList[$fileData['after']]['include'][] = $fileData['name'];
            }
            else{
                $resultFileList[] = $fileList[$key]['name'];
                if (array_key_exists('include', $fileList[$key])){
                    foreach ($fileList[$key]['include'] AS $afterFile){
                        $resultFileList[] = $afterFile;
                    }
                }
            }
        }

        return $resultFileList;
    }

    /**
     * Metoda usuwa z listy plikow do comine, pliki ktore sa w wersji plain
     * @param array $fileList
     * @return array
     */
    protected function clearFileList(array $fileList)
    {
        if (array_key_exists('combine', $fileList) && array_key_exists('html', $fileList)){
            foreach ($fileList['combine'] AS $key=>$filePath){
                if (isSet($fileList['html'][$key])){
                    unset($fileList['combine'][$key]);
                }
            }
        }

        return $fileList;
    }

    /**
     * @param string $filePath
     * @return mixed
     */
    private function fileKey($filePath)
    {
        return basename($filePath);
    }

    /**
     * Zapisuje plik wyjsciowy
     * @param string $content
     */
    protected function saveOutputFile($content)
    {
        #Tworzymy plik wyjsciowy jesli go nie ma
        $info = pathinfo($this->getSavePath());

        if(array_key_exists('dirname', $info) && !is_dir($info['dirname'])){
            mkdir($info['dirname'], self::$OUTPUT_MODE, true);
        }

        @unlink($this->getSavePath());
        @file_put_contents($this->getSavePath(),$content);
    }

    /**
     * Buduje tresc do zapisu dla pliku wyjsciowego
     * @param array $filesList
     * @return string
     */
    protected function buildOutputContent(array $filesList)
    {
        $content='';
        foreach($filesList as $file){
            if (substr($file, 0, 4) == 'http'){
                $content.= file_get_contents($file);
            }else{
                $content.= file_get_contents(public_path($file));
            }
        }

        //Jezlei jest handler
        if($this->hasHandler()){
            //Prygotowac text handlerem
            $handler = $this->getHandler();
            $content = $handler($content);
        }

        return $content;
    }

    /**
     * Tworzy url dla pobierania wygenerowanego pliku
     * @return string
     */
    public function makeUrl()
    {
        $publicPath = public_path();

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
            if ( $publicPath[count($publicPath)-1] != '\\' ){
                $publicPath .= '\\';
            }
        }
        
        return asset(str_replace($publicPath,'',$this->getSavePath()));
    }

    /**
     * Zaladowac konfiguracje
     * @param array $config
     */
    public function loadConfig(array $config)
    {
        $this->checkConfig($config);

        
        $handler = unserialize($config['handler']);
        $savePath = unserialize($config['savePath']);

        $this->setPaths($config['paths']);
        
        $this->setHandler($handler);
        
        $this->setSavePath($savePath($this));
    }

    /**
     * Replaces  php() with eval values
     * @param string $content
     * @return mixed
     */
    public static function replacePhp($content)
    {
        $content = preg_replace_callback("/php\(\/\*(.*)\*\/\)/",function($matches){
            return eval("return json_encode(".$matches[1].');');
        },$content);
        
        return $content;
    }
    
    /**
     * Metoda sprawdza, czy sa wszystkie niezbledne elementy konfiguracyjne potrzebne do wygenerowania
     * pliku wyjsciowego
     *
     * @throws \Netinteractive\Combiner\NoConfigException
     * @throws \Netinteractive\Combiner\ConfigException
     */
    protected function checkConfig($config)
    {
        if (!is_array($config)){
            throw new NoConfigException();
        }

        foreach ($this->keysToCheck AS $keyName){
            if (!array_key_exists($keyName, $config)){
                throw new ConfigException(
                    $keyName,
                    $this->getSkin(),
                    $this->getType(),
                    $this->getMode()
                );
            }
        }
    }


    /**
     * @return mixed
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * @param mixed $skin
     */
    public function setSkin($skin)
    {
        $this->skin = $skin;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }


    /**
     * @return boolean
     */
    public function hasHandler()
    {
        return !empty($this->handler);
    }


    /**
     * @return \Closure
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param mixed $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @param array $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
    }

    /**
     * @param $path
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
    }

    /**
     * @return mixed
     */
    public function getSavePath()
    {
       return $this->savePath;
    }

    /**
     * @param mixed $savePath
     */
    public function setSavePath($savePath)
    {
        $this->savePath = $savePath;
    }



}
