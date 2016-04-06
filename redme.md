Netinteractive\Combiner
=======================

Paczka do budowania pojedyncznych plukow js i css w projekcie.

## Docs

Patrz do docs.md


## Changelog

#### 2.0.14
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
