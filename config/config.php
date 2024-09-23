<?php
declare(strict_types=1);

# [PL] plik zwraca tablice z konfiguracja odpowiednich modłów aplikacji takich jak baza danych, zabezpieczenie haseł, wysyłanie emaili, recaptcha, tworzenie logów
# [ENG] the file returns an array with the configuration of the appropriate application modules such as database, password security, sending emails, recaptcha, creating logs
return [
    'db'=>[
    'password'=>'Qwerty12345',
    'user'=>'root',
    'host'=>'mysql',
    'database'=>'notes2'
    ],
    'password'=>[ 
        'pepper'=>'P@1mN7#uLqJ3$Hr5sXe8W2cZoFvY6bGt'
    ],
    'mail'=>[
        'host'=>'smtp.gmail.com',
        'port'=>465,
        'user'=>'pawlina.server@gmail.com',
        'password'=>'xbtc jeog dumh rwhy',
        'charset'=>'UTF-8',
        'fromMail'=>'no-reply@notatki.pl',
        'fromName'=>'Notatki.pl',
        'replyToMail'=>'obsluga@notatki.pl',
        'replyToName'=>'Obsługa klienta'

    ],
    'recaptcha'=>[
        'secretKey'=>'6LcQ9DwoAAAAAKlVZUNymQdQV_G10PyuHBF1nbyQ'
    ],
    'file'=>['fileName'=>'src/Logger/log.txt'],
];
