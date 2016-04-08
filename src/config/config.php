<?php

$serializer = new SuperClosure\Serializer(null, '123Core2Combiner');

$makeSavePath = function(\Netinteractive\Combiner\Combiner $combiner){
    $mobilePrefix = '';
    if (isMobile()){
        $mobilePrefix = 'mobile_';
    }

    $path = 'combiner/'.$combiner->getSkin().'/'.$combiner->getType().'/'.\App::getLocale().'/'.$mobilePrefix.''.$combiner->getMode().'.'.$combiner->getType();

    return public_path($path);
};

$handleContent = function($js){
    return \Combiner::replacePhp($js);
};



return array(
    #key is a skin name
    'default' => array(
        'js' => array(
            'backend'=>array(
                #Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
                'savePath'=>  $serializer->serialize(function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                    return $makeSavePath($combiner);
                }),

                #Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler'=> $serializer->serialize(function($text) use ($handleContent){
                    return $handleContent($text);
                }),

                #pliki ktore theba zaladowac w pierwszej kolejnosci
                'paths'=>array(
                ),
            ),

            'frontend'=>array(
                'savePath' => $serializer->serialize(function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                    return $makeSavePath($combiner);
                }),
                'handler' => $serializer->serialize(function($text) use($handleContent){
                    return $handleContent($text);
                }),
                'type'=>'js',
                'paths'=>array(
                 ),
            )
        ),
        'css' => array(
            'backend' => array(
                #Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
                'savePath'=>  $serializer->serialize(function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                    return $makeSavePath($combiner);
                }),

                #Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler'=> $serializer->serialize(function($text) use ($handleContent){
                    return $handleContent($text);
                }),

                #pliki ktore theba zaladowac w pierwszej kolejnosci
                'paths'=>array(
                ),

            )
        ),
    ),

);