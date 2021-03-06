Packa słuzy dla połączenia plików js i css w jeden plik.

Laduje zarowno pliki lokalne jak i zdalne.
W przypadku podania calego katalogu, zostana zaladowane wszystkie pliki danego typu z tego katalogu jak i jego podkatalogow.

W konfigu mozemy skonfigurowac, czy dany plik mergowac tylko dla konkretnej wersji jezykowej lub mobile.

## Podstawowy prykład użycia

Zmergowanie plikow "css" oraz "js" skina "default" dla "backend"u:

    Layout:
        <link href="[[ \Combiner::load('default', 'backend', 'css')->combine() ]]" rel="stylesheet">
        
        <script src="[[ \Combiner::load('default', 'backend', 'js')->combine() ]]"></script>


W pliku konfiguracyjnym, glownym kluczem jest nazwa skina, nastepnie typ plikow, a dalej backend lub frontend.
Ponizszy przyklad zawartosc katalogu "packages/netinteractive/plugins" laduje tylko dla urzadzen mobilnych i tylko dla wersji "en".

    Konfig:
        return array(
            #key is a skin name
            'default' => array(
                'js'=>array(
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
                            array(
                                'path' => '/packages/netinteractive/plugins',
                                'mobile' => true,
                                'langs' => array('en')
                            ),
                            'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',
                            '/packages/netinteractive/jQuery/jquery.min.js',
                            '/packages/netinteractive/easyUi/jquery.easyui.min.js',
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
            ),
        
        );
        

## Dodanie plikow bez mergowania

    Layout:
          [[ \Combiner::load('default', 'backend', 'css')->html() ]]
            

    Konfig:
         return array(
                    #key is a skin name
                    'default' => array(
                        'css'=>array(
                            'backend'=>array(
                               
                                //pliki ktore theba zaladowac w pierwszej kolejnosci
                                'paths'=>array(
                                    array(
                                           'combine' => false,
                                           'path' =>  '/packages/netinteractive/admin/angulr/src/css/font.css'
                                       ),
                                ),
                            ),
                        ),
                    ),
                
                );
        


## Kolejność ładowania plików
Pliki ladowane sa w kolejnosci podanej w pliku konfiguracyjnym.
Jesli ladujemy pliki z calego katalogu, a checmy aby jeden z nich zaladowal sie przed innymi, to dodajemy go
do konfiga, przed wpisem dla katalogu.

Mozna wskazac konkretna nazwe pliku, po ktorej chcemy aby nasz plik zostal zaladowany:

       'paths'=>array(
            array(
                'combine' => true,
                'tag' => 'back',
                'path' =>   'packages/netinteractive/acl',
                'after' => 'agular.js'
            ),
        )
        
To, gdzie plik fizycznie sie znaduje, nie ma znaczenia. Liczy sie tylko jego nazwa.

## Tagowanie

Plik konfiguracyjny:

    return array(
        #key is a skin name
        'default' => array(
            'js' => array(
                'backend'=>array(
    
                    #pliki ktore theba zaladowac w pierwszej kolejnosci
                    'paths'=>array(
                        array(
                            'combine' => false,
                            'path' =>   '/packages/netinteractive/admin/'.AngulrServiceProvider::$modName.'/libs/jquery/bootstrap/dist/js/bootstrap.js',
                        ),
                        array(
                            'combine' => false,
                            'tag' => 'front',
                            'path' =>   '/packages/netinteractive/admin/'.AngulrServiceProvider::$modName.'/libs/angular/angular/angular.js',
                        ),
                )
            )
        )
    );
    
Layout (do metody html, przekazujemy nazwe taga):

     [[ \Combiner::load('default', 'backend', 'js')->html('front') ]]
    
    <script src="[[ \Combiner::load('default', 'backend', 'js')->combine() ]]"></script>

    [[ \Combiner::load('default', 'backend', 'js')->html() ]]
    

html('front') wstawi nam w layoucie tylko i wylacznie pliki otagowane "front".
Natomiast html() wstawi tylko i wylacznie pliki wogole nie otagowane.


## Modyfikacja wygenerowanego pliku
Chcemy zmodyfikować wygenerowany plik:

    array(
       'savePath'=>function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
           return public_path('all.js')
       },
       'handler'=>function($text){
            return obfuscate($text)
       }
    );
