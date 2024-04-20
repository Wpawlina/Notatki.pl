<?php
declare(strict_types=1);

namespace App\Mail;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use APP\Exception\ConfigurationException;
use APP\Exception\EmailException;
use Throwable;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';



# klasa mail handler jest odpowiedzalana za wysyłąnie emaili do tego korzysta z biblioteki PHPMailer 
class MailHandler
{

   
    private PHPMailer $mail;


    # kontstruktor tworzy obiekt MailHandlera i przypisuje odpowiednia konfiguracje biblioteki PHPMailer z pliku config.php
    public function __construct(array $config) {
        try
        {
        $this->validateConfig($config);
        $this->mail=new PHPMailer();
        $this->mail->isSMTP();
        //$this->mail->SMTPDebug = SMTP::DEBUG_SERVER; //wyswitla informacje o komuikacji z serwerem
        $this->mail->Host = $config['host'];
        $this->mail->Port = $config['port'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->SMTPAuth = true;
        $this->mail->Username =$config['user']; 
        $this->mail->Password = $config['password']; 
        $this->mail->CharSet = $config['charset'];
        $this->mail->setFrom($config['fromMail'], $config['fromName']);
        $this->mail->addReplyTo($config['replyToMail'], $config['replyToName']);
        $this->mail->isHTML(true);    
        }
        catch(Throwable $e)
        {
            throw new EmailException('Błąd MailHandler');
        }
    }
    


    #metoda odpowiedzalana za wysłanie emaila powitalnego dla nowego konta uzytkownika wraz z linkiem do aktywacji tego konta
    public function create(string $email,string $createCode):void
    {
        try
        {
            $this->mail->addAddress($email);
            $this->mail->Subject = 'Dziękujemy za założenie konta na Notatki.pl';
            $this->mail->Body = '
            <html>
                <body>
                    <h1>Dzień dobry!</h1>
                    <p>Dziekujemy za założenie konta na stronie Notatki.pl
                    </p>
                    <p>Potwierdz teraz swój email
                    <a href="http://testwp/~ubuadm/udemy/phptip/projekt_moj/index.php?action=activateUser&actCode='.$createCode.'"><button>Potwierdź!</button></a>
                    </p>
                    <hr>
                    <p>Administratorem Twoich danych osobowych jest:</p>
                    <p>Notatki.pl Sp.z.o.o, ul. Wiejska 4/6/8, 00-902 Warszawa</p>
                </body>
            </html>
	    	';

            $this->mail->AltBody=" Dzień dobry! \r\n
            Dziekujemy za założenie konta na stronie Notatki.pl \r\n
            Potwierdz teraz swój email wchodzac na stronę http://testwp/~ubuadm/udemy/phptip/projekt_moj/index.php?action=activateUser&actCode=$createCode
            ";
            $this->mail->send();

        }
        catch(Throwable $e)
        {
            throw new EmailException(" Błąd Wysyłania maila powitalnego do $email ");
        }
    }


    #metoda odpowiedzalna za wysłanie emaila z kodem zmiany hasła
    public function chgPasswd(string $email,string $chgCode):void
    {
        
        $this->mail->addAddress($email);
        $this->mail->Subject = 'Zmiana hasła w serwisie Notatki.pl';
        $this->mail->Body = '
        <html>
            <body>
                <h1>Witaj!</h1>
                <p>To jest link do zmiany hasła na stronie Notatki.pl
                </p>
                <p>Zresetuj swoje hasło
                <a href="http://testwp/~ubuadm/udemy/phptip/projekt_moj/index.php?action=chgPasswd&chgCode='.$chgCode.'"><button>Resetuj!</button></a>
                </p>
                <hr>
                <p>Administratorem Twoich danych osobowych jest:</p>
                <p>Notatki.pl Sp.z.o.o, ul. Wiejska 4/6/8, 00-902 Warszawa</p>
            </body>
        </html>
        ';

        $this->mail->AltBody=' Witaj! \r\n
        To jest link do zmiany hasła na stronie Notatki.pl \r\n
        Zresetuj swoje hasło wchodząc na stronę http://testwp/~ubuadm/udemy/phptip/projekt_moj/index.php?action=chgPasswd&chgCode='.$chgCode
        ;
        $this->mail->send();
    }

    #metoda odpowiedzalna za wysłanie emaila z informacja o zmianie hasła
    public function notifyPassword(string $email):void
    {
        $this->mail->addAddress($email);
        $this->mail->Subject = 'Zmiana hasła w serwisie Notatki.pl';
        $this->mail->Body = '
        <html>
            <body>
                <h1>Witaj!</h1>
                <p>Twoje hasło na stronie Notatki.pl zostało włąśnie zmienione
                </p>
                <hr>
                <p>Administratorem Twoich danych osobowych jest:</p>
                <p>Notatki.pl Sp.z.o.o, ul. Wiejska 4/6/8, 00-902 Warszawa</p>
            </body>
        </html>
        ';

        $this->mail->AltBody=' Witaj! \r\n
        Twoje hasło na stronie Notatki.pl zostało włąśnie zmienione\r\n'
        ;
        $this->mail->send();



    }

    #metoda odpowiedzakalna za sprawdzenie poprawnosci konfiguracji
    private function validateConfig(array $config ) :void
    {
        if(
            empty($config['host'])
            ||empty($config['port'])
            || empty($config['user'])
            || empty($config['password'])
            || empty($config['charset'])
            || empty($config['fromMail'])
            || empty($config['fromName'])
            || empty($config['replyToMail'])
            || empty($config['replyToName']) )
        {
            throw new ConfigurationException('Email configuration error');
        }
    }




}