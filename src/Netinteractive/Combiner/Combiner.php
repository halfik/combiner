<?php namespace Netinteractive\Combiner;



use Netinteractive\Utils\Utils;

class Combiner {
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
					$paths=array_merge($paths,\Netinteractive\Utils\Utils::scanDir($path,'.'.$extension,true));
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


//			$text=preg_replace("#/\*[^\/*]+\*/#i",'',$text);
//			$text=preg_replace("/\/\/.*\n/","",$text);
//			$text=str_replace("\t"," ",$text);
//			$text=str_replace("\n"," ",$text);
//			$text=str_replace("\r"," ",$text);
//			$text=str_replace("\r\n"," ",$text);
//			$text=preg_replace("/\s{2,}/"," ",$text);

			file_put_contents($toPath,$text);
		}

		$publicPath = public_path();

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
			if ( $publicPath[count($publicPath)-1] != '\\' ){
				$publicPath .= '\\';
			}
		}


		return asset(str_replace($publicPath,'',$toPath));

		/*$resFileName=$mode.'.'.\App::getLocale().'.'.$type;
		$path=public_path($resFileName);
		if(\Config::get('app.debug') || !file_exists($path)){
			$files=\Config::get('ball8.'.$type.'.'.$mode);
			$dir=public_path($mode.'/');


			foreach($files as &$file){
				$file=public_path($file);
			}

			$text=\Netinteractive\Utils\Utils::glueFiles($dir,'.'.$type,null,$files);
			$text=self::phpEval($text);
			file_put_contents($path,$text);
		}
		return asset($resFileName);*/
	}

	static function phpEval($t){
		$t=preg_replace_callback("/php\(\/\*(.*)\*\/\)/",function($matches){
			return eval("return json_encode(".$matches[1].');');
		},$t);
		return $t;
	}
}
