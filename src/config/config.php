<?php
return array(
    //Sciezka do skinów
    'skinsPath'=>public_path('app/frontend'),

    //Prefix do sceizki z wygenerowanym plikiem
    'prefix'=>function(){
        return DIRECTORY_SEPARATOR.\App::getLocale().DIRECTORY_SEPARATOR;
    },

    //Handler do minify albo obfuscate
    'handler'=>function($text){
        $text=preg_replace_callback("/php\(\/\*(.*)\*\/\)/",function($matches){
            return eval("return json_encode(".$matches[1].');');
        },$text);
        return $text;
    },

    //Typ plikow do zaladowania
    'type'=>'js',

    'paths'=>array(
        public_path('packages/netinteractive/jQuery/jquery.min.js'),
        public_path('packages/netinteractive/easyUi/jquery.easyui.min.js'),
        'test.js'
    )
);