<?php
return array(
    'js'=>array(
        'backend'=>array(
            //Skin lub array skinów ktore dzidzicza jeden po drugin
            'skins'=>'default',

            //Sciezka do skinów
            'skinsPath'=>public_path('app/backend/'),

            //Typ plikow do zaladowania
            'type'=>'js',

            //Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
            'savePath'=>function(\Netinteractive\combiner\Combiner $combiner){
                $skins=$combiner->getSkins();
                return public_path('combiner/backend/'.\App::getLocale().'/'.array_pop($skins).'.js');
            },

            //Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
            'handler'=>function($text){
                $text=preg_replace_callback("/php\(\/\*(.*)\*\/\)/",function($matches){
                    return eval("return json_encode(".$matches[1].');');
                },$text);
                return $text;
            },


            //pliki ktore theba zaladowac w pierwszej kolejnosci
            'paths'=>array(
                public_path('packages/netinteractive/jQuery/jquery.min.js'), //Zewnwntzny pkik z packi
                public_path('packages/netinteractive/easyUi/jquery.easyui.min.js'),
                'test.js' //wewnetzny plik ze skina (zalasuje sie z ostaniego skina jezeli jest, jezeli nie to z poprzedniego)
            )
        ),

        'frontend'=>array(
            'skins'=>'default',
            'skinsPath'=>public_path('app/frontend/'),
            'savePath'=>function(\Netinteractive\combiner\Combiner $combiner){
                $skins=$combiner->getSkins();
                return public_path('combiner/frontend/'.\App::getLocale().'/'.array_pop($skins).'.js');
            },
            'handler'=>function($text){
                $text=preg_replace_callback("/php\(\/\*(.*)\*\/\)/",function($matches){
                    return eval("return json_encode(".$matches[1].');');
                },$text);
                return $text;
            },
            'type'=>'js',
            'paths'=>array(
                public_path('packages/netinteractive/jQuery/jquery.min.js'),
                'test.js'
            )
        )
    ),
);