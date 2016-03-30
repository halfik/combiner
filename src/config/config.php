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

$handleJs = function($js){
    $js = preg_replace_callback("/php\(\/\*(.*)\*\/\)/",function($matches){
        return eval("return json_encode(".$matches[1].');');
    },$js);

    return $js;
};

return array(
    #key is a skin name
    'default' => array(
        'js' => array(
            'backend'=>array(
                //Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
                'savePath'=>  $serializer->serialize(function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                    return $makeSavePath($combiner);
                }),

                //Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler'=> $serializer->serialize(function($text) use ($handleJs){
                    return $handleJs($text);
                }),

                //pliki ktore theba zaladowac w pierwszej kolejnosci
                'paths'=>array(
                    public_path('packages/netinteractive/jQuery/jquery.min.js'),
                ),
            ),

            'frontend'=>array(
                'savePath' => $serializer->serialize(function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                    return $makeSavePath($combiner);
                }),
                'handler' => $serializer->serialize(function($text) use($handleJs){
                    return $handleJs($text);
                }),
                'type'=>'js',
                'paths'=>array(
                    public_path('packages/netinteractive/jQuery/jquery.min.js'),

                )
            )
        ),
        'css' => array(
            'backend' => array(
                //Funkcja do generownia sciezki dla zapisywania wygenerowanego pliku
                'savePath'=>  $serializer->serialize(function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
                    return $makeSavePath($combiner);
                }),

                //Handler dla modyfikownia sklejonego pliku (minify, obfuscat, etc)
                'handler'=> $serializer->serialize(function($text) use ($handleJs){
                    return $handleJs($text);
                }),

                //pliki ktore theba zaladowac w pierwszej kolejnosci
                'paths'=>array(
                    public_path('/packages/netinteractive/easyUi/themes/bootstrap/easyui.css'),
                    public_path('/packages/netinteractive/easyUi/themes/icon.css'),
                    public_path('/packages/netinteractive/easyUi/themes/color.css'),
                ),

            )
        ),
    ),

);