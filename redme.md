Netinteractive\Combiner
=======================

Paczka do budowania pojedyncznych plukow js i css w projekcie.

## Docs

Patrz do docs.md


## Changelog

#### 2.0.22 - 2.0.25
    Zamiana paczki "jeremeamia/superclosure" na "opis/closure"

#### 2.0.20 - 2.0.21
    Fix bledu, gdzie dla konfiguracji combine=true - nie mergowalo plikow.
    
#### 2.0.19
    Dopisalem mechanizm tagowania. Opis w docs.md

#### 2.0.18
    Pliki includowane w layout rozdzielane sa teraz znakiem nowej lini.

#### 2.0.17
    Zamiane tagow php(/**/) w wynik evala w mergowanych plikach, przenioslek jaki static do Combiner::replacePhp($content).

#### 2.0.16
    Zmienilem sposob deklaracji sciezek do plikow dla combine - teraz public_path jest automatycznie dodawany przez combiner w trakcie budowania contentu
    pliki wynikowe.

#### 2.0.14 - 2.0.15
    Usunalem array_unique. Unique wartosci zapewnia generowaniu tablicy w oparciu o klucze, gdzie kluczem jest nazwa pliku.
    Dodatkowo jesli plik zostal dodany jako combine=>false, a jakis jakis inny modul dodal go jako combiner=true, nie
    trafi on na liste plikow do zmerowania. Pliki do include maja priorytet nad plikami do mergea.

#### 2.0.13
    Fix bledu, gdzie Combiner->html() nie odroznila css od js.

#### 2.0.11 - 2.0.12
    Zmiana interfejsu dla combine. Dodalem tez mozliwosc budowania czystego htmla dla pojedynczych plikow.
    Przyklad i opis jest w docs.md

#### 2.0.7 - 2.0.10
    Fixed config merge.

#### 2.0.6
    Dodalem do konfiga, do funkcji odpowiedzialnej za dostarczenie sciezki i nazwy pliku output, obsluge dla mobile.

#### 2.0.5
    Przepisalnie paczki. Inny interfase combinera oraz sposob konfiguracji.

#### 2.0.2 - 2.0.3
    Dodalem super closury, aby opakowac funkcje anonimowe konfiga.
    Usunalem wszystkie typowania \Closure.

#### 2.0.1
    Wersja dla Laravel 5.
