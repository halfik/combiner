<?php

$makeSavePath=function($combiner, $mode){
    $skins=$combiner->getSkins();
    $skin=array_pop($skins);
    $skin=explode(DIRECTORY_SEPARATOR,realpath($skin));
    $skin=array_pop($skin);
    return public_path('combiner/'.$mode.'/'.\App::getLocale().'/'.$skin.'.js');
};

$handleJs=function($js){
    $js=preg_replace_callback("/php\(\/\*(.*)\*\/\)/",function($matches){
        return eval("return json_encode(".$matches[1].');');
    },$js);
    return $js;
};

return array(
    'js'=>array(
        'backend'=>array(
            //Sciezka do foldera ze skinem lub array scezek (jezeli jest array to pliki ze skinów beda nadpisane tej kolejnosci w ktorej sa w array)
            'skins'=>public_path('app/backend/default/'),

            //Typ plikow do zaladowania
            'type'=>'js',

            //Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
            'savePath'=>function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                return $makeSavePath($combiner, 'backend');
            },

            //Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
            'handler'=>function($text) use ($handleJs){
                return $handleJs($text);
            },

            //pliki ktore theba zaladowac w pierwszej kolejnosci
            'paths'=>array(
                public_path('packages/netinteractive/jQuery/jquery.min.js'), //Zewnwntzny pkik z packi
                public_path('packages/netinteractive/easyUi/jquery.easyui.min.js'),
                'test.js' //wewnetzny plik ze skina (zalasuje sie z ostaniego skina jezeli jest, jezeli nie to z poprzedniego)
            )
        ),

        'frontend'=>array(
            'skins'=>public_path('app/frontend/default/'),
            'savePath'=>function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                return $makeSavePath($combiner, 'frontend');
            },
            'handler'=>function($text) use($handleJs){
                return $handleJs($text);
            },
            'type'=>'js',
            'paths'=>array(
                public_path('packages/netinteractive/jQuery/jquery.min.js'),
                'test.js'
            )
        )
    ),
);