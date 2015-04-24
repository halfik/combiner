<?php namespace Netinteractive\Combiner;

use Netinteractive\Utils\Utils;


class Combiner implements  \Netinteractive\Combiner\Interfaces\Combiner{

	static function incFile($fromPaths, $toPath, $extension=null){

		if(!is_array($fromPaths)){
			$fromPaths=array($fromPaths);
		}
		$toPath=public_path($toPath);
		$pathInfo=pathinfo($toPath);
		$fName=$pathInfo['basename'];
		$fName=explode('.',$fName);
		if(!$extension){
			$extension=array_pop($fName);
		}
		else{
			array_pop($fName);
		}

		$fName=implode('.',$fName).'.'.\Gettext::getLocale().'.'.$extension;
		$toPath=$pathInfo['dirname'].DIRECTORY_SEPARATOR.$fName;


		$paths=array();

		if(\Config::get('app.debug') || !file_exists($toPath)){
			foreach($fromPaths as $path){

				$path=public_path($path);

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

		$publicPath = public_path();

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
			if ( $publicPath[count($publicPath)-1] != '\\' ){
				$publicPath .= '\\';
			}
		}


		return asset(str_replace($publicPath,'',$toPath));
	}
}
