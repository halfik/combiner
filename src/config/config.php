<?php

use Opis\Closure\SerializableClosure;

$makeSavePath=function($combiner, $mode){
    $skins = $combiner->getSkins();
    $skin = array_pop($skins);
    $skin = explode(DIRECTORY_SEPARATOR,realpath($skin));
    $skin = array_pop($skin);

    return public_path('combiner/'.$mode.'/'.\App::getLocale().'/'.$skin.'.js');
};

$handleContent = function($js){
    return \Combiner::replacePhp($js);
};

return array(
    'default' => array(
        'js'=>array(
            'backend'=>array(
                 #Sciezka do foldera ze skinem lub array scezek (jezeli jest array to pliki ze skinów beda nadpisane tej kolejnosci w ktorej sa w array)
                'skins'=>public_path('app/backend/default/'),


                #Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
                'savePath' => serialize(new SerializableClosure(
                        function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                            return $makeSavePath($combiner, 'backend');
                        })
                ),


                #Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler' => serialize(new SerializableClosure(
                        function($text) use ($handleContent){
                            return $handleContent($text);
                        })
                ),


                #pliki ktore theba zaladowac w pierwszej kolejnosci
                'paths'=>array()
            ),

            'frontend'=>array(
                'skins'=>public_path('app/frontend/default/'),

                #Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
                'savePath' => serialize(new SerializableClosure(
                        function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                            return $makeSavePath($combiner, 'backend');
                        })
                ),


                #Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler' => serialize(new SerializableClosure(
                        function($text) use ($handleContent){
                            return $handleContent($text);
                        })
                ),

                'paths'=>array()
            )
        ),
    ),
    'css'=>array(
    )
);