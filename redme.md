#Documentacja

##Lączenie plików
Paczka łączy pliki w jedyn plik i pozwala uzywac w plikach funkcje php
pierszy parametr to arraj plików, drugi to plik (w public) do ktorego zapiszą sie polączone pliki z array

    <script src="<?php echo\Combiner::incFile(array(
          '/global/plugins/jquery-ui/i18n/datepicker-pl.js',
          'packages/netinteractive/plugins/ni.js',
          'packages/netinteractive/plugins/Plugin.js',
          'packages/netinteractive/crud/js/admin',
          'packages/netinteractive/plugins/ui',
          'global/scripts/init.js',
          'global/scripts/app/patient',
          'global/scripts/app/visit',
          'global/scripts/app/slot',
          'global/scripts/app/reception',
          'admin/patient',
      ),'combiner.js')?>"></script>

Mozna wskazac jak osobne pliki tak i cale foldery wtegy paczka lączy wsyskie pliki kotre są q folderze i w child-folderach

W trybie dev combiner lączy pliki pzy kazdym zapytaniu, na produkcji tylki w przypadku jezeli wygenerowanego pliku nie
istnieje


##Oczyszczenie wygenerowanych plikow

    php artisan combiner:clean maska [potwierdzenie]

* maska - maska pliku (na prykład combiner*)
* potwierdzenie - jezeli nie jest wskazany to bedzie pokazana lista plików ktore beda usuniente do potwerdzenia


## Changelog


#### 2.0.0
* wersja dla Lararavel 5

#### 1.0.3
* zmiana sposobu pobierania pliku jezykowego na: \Gettext::getLocale()

#### 1.0.1
* dodana komenda combiner:clear
* dodana dokumentacjia