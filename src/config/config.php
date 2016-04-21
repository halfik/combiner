<?php

use Opis\Closure\SerializableClosure;

$makeSavePath  = serialize(new SerializableClosure(
        function($combiner, $mode){
            $skin = $combiner->getSkin();
            $type = $combiner->getType();

            return public_path('combiner/'.$mode.'/'.\App::getLocale().'/'.$skin.'.'.$type);
        }
    )
);


$handleContent  = serialize(new SerializableClosure(
        function($content){
            return \Combiner::replacePhp($content);
        }
    )
);

return array(
    'default' => array(
        'js'=>array(
            'backend'=>array(
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