<?php

use Opis\Closure\SerializableClosure;

$makeSavePath  = serialize(new SerializableClosure(
        function($combiner, $mode){
            $skin = $combiner->getSkin();

            return public_path('combiner/'.$mode.'/'.\App::getLocale().'/'.$skin.'.js');
        }
    )
);


$handleContent  = serialize(new SerializableClosure(
        function($js){
            return \Combiner::replacePhp($js);
        }
    )
);

return array(
    'default' => array(
        'js'=>array(
            'backend'=>array(
                #Sciezka do foldera ze skinem lub array scezek (jezeli jest array to pliki ze skinów beda nadpisane tej kolejnosci w ktorej sa w array)
                'skins'=>public_path('app/backend/default/'),


                #Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
                'savePath' => serialize(new SerializableClosure(
                        function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                            $makeSavePathFunc = unserialize($makeSavePath);
                            return $makeSavePathFunc($combiner, 'backend');
                        })
                ),


                #Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler' => serialize(new SerializableClosure(
                        function($text) use ($handleContent){
                            $handleContentFunc = unserialize($handleContent);
                            return $handleContentFunc($text);
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
                            $makeSavePathFunc = unserialize($makeSavePath);
                            return $makeSavePathFunc($combiner, 'frontend');
                        })
                ),


                #Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler' => serialize(new SerializableClosure(
                        function($text) use ($handleContent){
                            $handleContentFunc = unserialize($handleContent);
                            return $handleContentFunc($text);
                        })
                ),

                'paths'=>array()
            )
        ),
        'css'=>array(
            'backend'=>array(
                #Sciezka do foldera ze skinem lub array scezek (jezeli jest array to pliki ze skinów beda nadpisane tej kolejnosci w ktorej sa w array)
                'skins'=>public_path('app/backend/default/'),


                #Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
                'savePath' => serialize(new SerializableClosure(
                        function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                            $makeSavePathFunc = unserialize($makeSavePath);
                            return $makeSavePathFunc($combiner, 'backend');
                        })
                ),


                #Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler' => serialize(new SerializableClosure(
                        function($text) use ($handleContent){
                            $handleContentFunc = unserialize($handleContent);
                            return $handleContentFunc($text);
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
                            $makeSavePathFunc = unserialize($makeSavePath);
                            return $makeSavePathFunc($combiner, 'frontend');
                        })
                ),


                #Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler' => serialize(new SerializableClosure(
                        function($text) use ($handleContent){
                            $handleContentFunc = unserialize($handleContent);
                            return $handleContentFunc($text);
                        })
                ),

                'paths'=>array()
            )
        ),
    ),
);