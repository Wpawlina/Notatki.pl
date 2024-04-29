Projekt Notatki.pl to aplikacja oferujacy uzytkownikom serwis internetowy do tworzenia notatek które sa przechowywane na serwerzę i dostepne w internecie. Serwis po stworzeniu przez uzytkownika konta umozliwia mu tworzenie,  edytowanie i usuwanie  notatek należacych do jego konta. Wyszystkie notatki są przechowywane na serwerze i uzytkownik ma zawsze dostep do wszytkich swoich notatek.
Aplikacja napisana jest w php bez użycia frameworków, jest zbudowana w modelu MVC i architekturze monolit. Serwis obecnie  działa na serwerze linuks ubuntu wraz z serwerem apache, do przechowywania danych wykorzystuje serwer MySQL oraz do obsługi emaili wykorzytuje serwer smtp google gmail. W aktualnej formie serwis znajduje sie dalej w dewelopmencie i jest udostepniony tylko w lokalnej sieci do testów.

Przed uruchomieniem projektu lokalnie, upewnij się, że na Twoim serwerze zainstalowane są:
- PHP (wersja: PHP 8.0 lub nowsza)
- Serwer Apache lub inny serwer HTTP obsługujący PHP
- Baza danych MySQL lub inna baza danych wspierana przez PHP
- serwer mailowy lub zewnetrzną usługę wysyłania emaili

Nastepnie zaimportuj plik z baza danych do swojego serwera SQL i skonfiguruj połączenie z bazą danych, edytując plik `config.php` i wprowadzając odpowiednie dane dostępowe do Twojej bazy danych i serwera mailowego.
Moduł recaptcha został wyłączony na potrzeby prezentacji aplikacji, istneje mozliwosc jego ponownego uruchomienia po zarjestrowaniu domeny w serwisie googyl recaptcha i wpisaniu odpowiednik kluczy w `config.php` oraz odkomentowaniu odpowiednich czesci kodu oznaczonych recaptcha
Nastepnie uruchom aplikacje na serwerze HTTP i zezwolenie serwerowi na edycje pliku log.txt
do testowania aplikacji można użyc konta wojtek.pawlina3@gmail.com z hasłem moje#Notatki25



Uruchominie poprzez  docker
Moduł recaptcha został wyłączony na potrzeby prezentacji aplikacji, istneje mozliwosc jego ponownego uruchomienia po zarjestrowaniu domeny w serwisie googyl recaptcha i wpisaniu odpowiednik kluczy w `config.php` oraz odkomentowaniu odpowiednich czesci kodu oznaczonych recaptcha
W dokerze również nie działa samo wysyłanie emaili
Urchomienie 
- przejdz do folderu z aplikacja i wykonaj komende docker-compose up -d
- wpisanie w polu exec kontenera php_8.2_apache_container skrytu chmod -R 777 ../html
- wpisania w przegladarke adresu localhost:8081
- do testowania aplikacji można użyc konta wojtek.pawlina3@gmail.com z hasłem moje#Notatki25
- aby zalogowac sie do phpMyAdmin wejdz na localhost:8080 i  użyj konta root z hasłem Qwerty12345














