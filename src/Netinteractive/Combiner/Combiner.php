<?php namespace Netinteractive\Combiner;

use Netinteractive\Utils\Utils;
use Netinteractive\Combiner\Interfaces\CombinerInterface;


class Combiner implements  CombinerInterface{

    //Sciezka do skinów
    protected $skinsPaths;

    //Skiny z ktorych theba skombinowac pkik
    protected $skins=array();

    //Prefix do sceizki z wygenerowanym plikiem
    protected $prefix;

    //Handler do wygenerownego pliku (minify albo obfuscate)
    protected $handler;

    //pliki do zalodownia
    protected $paths=array();

    //typ plikow do ladowanie
    protected $type;

    public static function includeFiles($config){
        $combiner = new self($config);
        return $combiner->combine();
    }

    public function __construct($config=null)
    {
        if($config){
            $this->skinsPaths=$config['skinsPaths'];
            $this->paths=$config['paths'];
            $this->prefix=$config['prefix']();
            $this->handler=$config['handler'];
            $this->type=$config['type'];
            $this->skins=$config['skins'];
        }
    }

    public function setSkins(array $skins){
        $this->skins=$skins;
        return $this;
    }

    public function addSkin($skin){
        $this->skins[]=$skin;
        return $this;
    }

    public function setSkinPath($skinsPath){
        $this->skinsPath=$skinsPath;
        return $this;
    }

    /**
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix=$prefix;
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
     * Lączy pliki w jeden
     * @param array|string $fromPaths
     * @param string $toPath
     * @param string $extension
     */
    static function glueFiles($fromPaths, $toPath, $extension=null){
        if(!is_array($fromPaths)){
            $fromPaths=array($fromPaths);
        }
        $pathInfo=pathinfo($toPath);
        $fName=$pathInfo['basename'];
        $fName=explode('.',$fName);
        if(!$extension){
            $extension=array_pop($fName);
        }
        else{
            array_pop($fName);
        }

        $paths=array();

        if(\Config::get('app.debug') || !file_exists($toPath)){
            foreach($fromPaths as $path){

                if(is_dir($path)){
                    $paths=array_merge($paths,Utils::scanDir($path,'.'.$extension,true));
                }
                else{
                    $paths[]=$path;
                }

            }
            $paths=array_unique($paths);
            $text='';
            foreach($paths as $path){
                $text.=file_get_contents($path)."\n";
            }

            file_put_contents($toPath,$text);
        }
    }

    public function combine(){
        foreach($this->skins as $skin){
            $skinPath=$this->skinsPaths.$skin;
            $skinFiles=Utils::scanDir($skinPath,'.'.$this->type,true);
            foreach($skinFiles as $skinFile){
                $skinFile=str_replace($skinPath.DIRECTORY_SEPARATOR,'',$skinFile);
                if(!in_array($skinFile,$this->paths)){
                    $this->paths[]=$skinFile;
                }
            }
        }
        $realPaths=array();
        foreach($this->paths as $path){
            foreach($this->skins as $skin){

                $realPath=public_path($this->skinsPaths.'/'.$skin.'/'.$path);
                if(is_file($realPath)){
                    $realPaths[$path]=$realPath;
                }
            }
        }

        debug($realPaths);

    }

}
