Packa słuzy dla połączenia plików js i css w jeden plik

##Podstawowy prykład użycia
Mamy strone wsyskie javascripty dla ktorej znaduja sie w folderze **public/js/default/**
chcemy załadowac te wsyzskie js jako jeden plik **public/all.js**.

Zeby to zrobic musimy stworyc configuracyjny plik **config/combiner.php**:

    array(
       'skins'=>public_path('public/js/default/'),
       'type'=>'js',
       'savePath'=>function(\Netinteractive\combiner\Combiner $combiner){
           return public_path('all.js')
       }
    );
    
I dodac do **resources/views/index.blade.php**

<script src="<?php echo \Combiner::includeFiles(\Config::get('combiner'))?>"></script>

##Kolejność ładowania plików
Logika naszych js wymbaga żeby plik **public/js/default/list.js** ladował sie przed plikiem **public/js/default/calendar.js**

    array(
       'skins'=>public_path('public/js/default/'),
       'type'=>'js',
       'savePath'=>function(\Netinteractive\combiner\Combiner $combiner){
           return public_path('all.js')
       },
       paths=>array(
            'list.js',
            'calendar.js'
       )
    );
    
W taki sposob mozemy wskazac kolejnosc nie tylko plikow a i folderow

##Ładowanie zewnentzych plików
Chcemy do naszego pliku dodać plik jquery.js ktory się znajduje poza folderem ze skinem i musi byc zaladowany w pirwszej kolejnosci

    array(
       'skins'=>public_path('public/js/default/'),
       'type'=>'js',
       'savePath'=>function(\Netinteractive\combiner\Combiner $combiner){
           return public_path('all.js')
       },
       paths=>array(
            public_path('packages/netinteractive/jQuery/jquery.min.js'),
            'list.js',
            'calendar.js'
       )
    );
W taki sposob mozemy ladowac nie tylko pliki a i foldery

##Kombinacja pliku z różnych skinów
Potrebujemy stwozyc skin który zdiała tak samo jak defoltowy za wyjątkiem pliku calendar.js i dodatkowej funkcionalnosci planner.js ktorej nie potrzebujemy w
defoltowym skinie

- Tworzymy nowy folder **public/js/extend/**
- Dodjemy do tego foledry plik **canedar.js** z modyfikowną funkcionalnoscią i plik **planner.js** z nową funkcionalnscia
- Tworzymy plik konfiguracyjny


    array(
       'skins'=>array(public_path('public/js/default/'),public_path('public/js/extend/')),
       'type'=>'js',
       'savePath'=>function(\Netinteractive\combiner\Combiner $combiner){
           return public_path('all.js')
       },
    );

Teraz kombiner podłaczy wsyskie pliki ktore są z foldera *public/js/default/* za wyjatkiem tych plików ktore są w **public/js/extend/**
Czyli logika jest taka jezeli pliku nie ma w folderze, szukaj plik w popzerdnim folderze.

#Modyfikacja wygenerowanego pliku
Chcemy zmodyfikować wygenerowany plik, napzyklad wyciac niepotzebne spacje albo zrobic obfuscacjie

    array(
       'skins'=>array(public_path('public/js/default/'),public_path('public/js/extend/')),
       'type'=>'js',
       'savePath'=>function(\Netinteractive\combiner\Combiner $combiner) use ($makeSavePath){
           return public_path('all.js')
       },
       'handler'=>function($text){
            return obfuscate($text)
       }
    );
