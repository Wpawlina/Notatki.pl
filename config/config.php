<?php
declare(strict_types=1);

#plik zwraca tablice z konfiguracja odpowiednich modłów aplikacji takich jak baza danych, zabezpieczenie haseł, wysyłanie emaili, recaptcha, tworzenie logów

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
        'user'=>'######',
        'password'=>'######',
        'charset'=>'UTF-8',
        'fromMail'=>'no-reply@notatki.pl',
        'fromName'=>'Notatki.pl',
        'replyToMail'=>'obsluga@notatki.pl',
        'replyToName'=>'Obsługa klienta'

    ],
    'recaptcha'=>[
        'secretKey'=>'#########'
    ],
    'file'=>['fileName'=>'src/Logger/log.txt'],
];
