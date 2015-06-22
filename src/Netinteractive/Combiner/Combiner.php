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
	static function incFile($fromPaths, $toPath, $extension=null){

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

    static function includeSkin($paths){

    }
}
