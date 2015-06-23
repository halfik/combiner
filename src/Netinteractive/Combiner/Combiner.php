<?php namespace Netinteractive\Combiner;

use Netinteractive\Utils\Utils;
use Netinteractive\Combiner\Interfaces\CombinerInterface;


class Combiner implements  CombinerInterface{

    //Scezki do skinow z ktorych ma byc wygenerowany plik
    protected $skins=array();

    //Handler do wygenerownego pliku (minify albo obfuscate)
    protected $handler;

    //pliki do zalodownia
    protected $paths=array();

    //sciezka do ktorej trzeba zapisac wygenerowany plik
    protected $savePath;

    //typ plikow do ladowanie
    protected $type;


    /**
     * Tworzy objekt na podstawie konfigu, laczy pliki i zwraca url do polącanego pliku
     * @param array $config
     * @return string
     */
    public static function includeFiles($config){
        $combiner = new self($config);
        if(\Config::get('app.debug') || !is_file($combiner->getSavePath())){
            $combiner->combine();
        }
        return $combiner->makeUrl();
    }

    /**
     * @return array
     */
    public function getSkins(){
        return $this->skins;
    }

    /**
     * @return mixed
     */
    public function getSavePath(){
        return $this->savePath;
    }

    /**
     * Tworzy url dla pobierania wygenerowanego pliku
     * @return string
     */
    public function makeUrl(){
        $publicPath = public_path();

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
            if ( $publicPath[count($publicPath)-1] != '\\' ){
                $publicPath .= '\\';
            }
        }

        return asset(str_replace($publicPath,'',$this->getSavePath()));
    }

    /**
     * @param $savePath
     * @return $this
     */
    public function setSavePath($savePath){
        $this->savePath=$savePath;
        return $this;
    }

    /**
     * @param null $config
     */
    public function __construct($config=null)
    {
        if($config){
            $this->loadConfig($config);
        }
    }

    /**
     * Zaladowac konfiguracje
     * @param array $config
     */
    public function loadConfig(array $config)
    {
        $this->setPaths($config['paths']);
        $this->setHandler($config['handler']);
        $this->setType($config['type']);
        $this->setSkins($config['skins']);
        $this->setSavePath($config['savePath']($this));
    }

    /**
     * Ustawic sciezki
     * @param array $paths
     * @return $this
     */
    public function setPaths(array $paths){
        $this->paths=$paths;
        return $this;
    }

    /**
     * Ustawic skiny
     * @param $skins
     * @return $this
     */
    public function setSkins($skins){
        if(!is_array($skins)){
            $skins=array($skins);
        }
        $this->skins=$skins;
        return $this;
    }

    /**
     * Dodac skin
     * @param $skin
     * @return $this
     */
    public function addSkin($skin){
        $this->skins[]=$skin;
        return $this;
    }


    /**
     * @param callable $handler
     * @return $this
     */
    public function setHandler(\Closure $handler){
        $this->handler=$handler;
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type=$type;
        return $this;
    }


    /**
     * Dodaje lkik do plików ktore trzeba zaladowac
     * @param string $path - sciezka do pliku wedlug foldery ze skinami
     * @return $this
     */
    public function addPath($path)
    {
        $this->paths[]=$path;
        return $this;
    }


    /**
     * Laczy pliki w jeden
     * @return $this
     */
    public function combine(){
        //Prygotwac liste plikow ze wszykich skinow ktore treba podloaczyc
        foreach($this->skins as $skinPath){
            $skinFiles=Utils::scanDir(realpath($skinPath),'.'.$this->type,true);
            foreach($skinFiles as $skinFile){
                $skinFile=str_replace(realpath($skinPath).DIRECTORY_SEPARATOR,'',$skinFile);
                if(!in_array($skinFile,$this->paths)){
                    $this->paths[]=$skinFile;
                }
            }
        }

        //Prygotowac liste plikow skombinowanych ze wszykticz skinow i zewnentrzych plikow
        $realPaths=array();
        foreach($this->paths as $path){
            //Jezeli plik jest zewnentzny (nie ze skina a na pryzklad z paczki)
            if(strpos($path, public_path())===0){
                if(is_dir($path)){
                    $realPaths=array_merge($realPaths, Utils::scanDir(realpath($path),'.'.$this->type,true));
                }
                else{
                    $realPaths[]=$path;
                }
            }
            else{
                //Dodac plik z potrebnego skina
                foreach($this->skins as $skinPath){
                    $realPath=$skinPath.$path;
                    if(is_dir($realPath)){
                        $dirPaths=Utils::scanDir(realpath($realPath),'.'.$this->type,true);
                        foreach($dirPaths as $dirPath){
                            $pathKey=str_replace($skinPath,'',$dirPath);
                            $realPaths[$pathKey]=$dirPath;
                        }
                    }
                    elseif(is_file($realPath)){
                        $realPaths[$path]=$realPath;
                    }

                }
            }

        }

        //Poloczyc zawartosc plikow
        $realPaths=array_unique($realPaths);
        $text='';
        foreach($realPaths as $path){
            $text.=file_get_contents($path);
        }

        //Jezlei jest handler
        if($this->handler){
            //Prygotowac text handlerem
            $handler=$this->handler;
            $text=$handler($text);
        }

        //Stworyc folder dla zapisywania pliku
        $info=pathinfo($this->savePath);
        if(!is_dir($info['dirname'])){
            mkdir($info['dirname'],0777,true);
        }


        //Zapisac plik
        file_put_contents($this->savePath,$text);
        return $this;
    }

}
