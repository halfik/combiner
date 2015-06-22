<?php namespace Netinteractive\Combiner;

use Netinteractive\Utils\Utils;
use Netinteractive\Combiner\Interfaces\CombinerInterface;


class Combiner implements  CombinerInterface{

    /**
     * Skleja i zapisjue pliki do jedengo plika i zwraca url do tego pliku
     * @param array $fromPaths - lista plikow lub folderow
     * @param string $toPath - plik do ktorego trzeba zapisac
     * @param null $extension - rozrzezenie pliku
     * @return string - url zapisanego pliku
     */
	static function includeFile($fromPaths, $toPath, $extension=null){

		static::glueFiles($fromPaths, $toPath, $extension);

		$publicPath = public_path();

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
			if ( $publicPath[count($publicPath)-1] != '\\' ){
				$publicPath .= '\\';
			}
		}

		return asset(str_replace($publicPath,'',$toPath));
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

            $text=preg_replace_callback("/php\(\/\*(.*)\*\/\)/",function($matches){
                return eval("return json_encode(".$matches[1].');');
            },$text);


            file_put_contents($toPath,$text);
        }
    }

    /**
     * Laczy pliki ze skina
     * @param string|array $skins - skiny z krotuch trzeba polaczyc pkiki ('default','red') (napierw szuka w red, jak nie ma w red to w default)
     * @param string $type - typ pliky
     * @param array $paths - pliki i foldery ktore treba podlaczyc najpierw (zeby mozna bylo zdefinijowac kolejnosc podlaczenia plikow)
     * @param string $mode - tryb frontend czy backend
     */
    static function includeSkin($skins, $type='js', $paths=array(), $mode='frontend'){
        if(!is_array($skins)){
            $skins=array();
        }
        foreach($skins as $skin){
            $skinPath=public_path('app/'.$mode.'/'.$skin);
            $skinFiles=Utils::scanDir($skinPath,'.'.$type,true);
            foreach($skinFiles as $skinFile){
                $skinFile=str_replace($skinPath,'',$skinFile);
                if(!in_array($skinFile,$paths)){
                    $paths[]=$skinFile;
                }
            }
        }
    }
}
