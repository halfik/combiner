<?php namespace Netinteractive\Combiner;

use Netinteractive\Utils\Utils;


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
    public function make($skin, $mode, $type)
    {
        $this->setSkin($skin);
        $this->setMode($mode);
        $this->setType($type);

        $config =  \Config("packages.netinteractive.combiner.config.$skin.$type.$mode");
        $this->loadConfig($config);

       if( \Config::get('app.debug') || !file_exists($this->getSavePath())){
           $this->combine();
       }

       return $this->makeUrl();
    }

    /**
     * Laczy pliki w jeden
     * @return $this
     */
    public function combine()
    {
        $filesList = array();
        foreach($this->getPaths() AS $path){
            $currentPath = null;

            # sprawdzamy czy path mamy jako bezposrednia sciezke, czy tez tablice konfiguracyjna
            if (is_array($path)){
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
            }
            else{
                $currentPath = $path;
            }

            #mozemy nie miec sciezki jesli jest do plik dla konkretnej wersji jezykowej, a nie jest to wersja
            #aktualnie uzywana przez aplikacje
            if ($currentPath){
                if ( is_dir($currentPath) ){
                    $dirFiles = \Utils::scanDir($path, array('f', 'd'), true);


                    foreach ($dirFiles AS $filePath){
                        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                        if ($ext == $this->getType()){
                            $filesList[] = $filePath;
                        }
                    }
                }
                else{
                    $filesList[] = $currentPath;
                }
            }

        }

        #unique
        $filesList = array_unique($filesList);

        #budowanie tresci
        $content = $this->buildOutputContent($filesList);

        #zapis
        $this->saveOutputFile($content);
    }


    /**
     * Zapisuje plik wyjsciowy
     * @param string $content
     */
    protected function saveOutputFile($content)
    {
        //Tworzymy plik wyjsciowy jesli go nie ma
        $info = pathinfo($this->getSavePath());

        if(array_key_exists('dirname', $info) && !is_dir($info['dirname'])){
            mkdir($info['dirname'], self::$OUTPUT_MODE, true);
        }

        file_put_contents($this->getSavePath(),$content);
    }

    /**
     * Buduje tresc do zapisu dla pliku wyjsciowego
     * @param array $filesList
     * @return string
     */
    protected function buildOutputContent(array $filesList)
    {
        $content='';
        foreach($filesList as $filePath){
            $content.= file_get_contents($filePath);
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

        $serializer = new \SuperClosure\Serializer;

        $handler = $serializer->unserialize($config['handler']);
        $savePath = $serializer->unserialize($config['savePath']);

        $this->setPaths($config['paths']);
        $this->setHandler($handler);
        $this->setSavePath($savePath($this));
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
