<?php

declare(strict_types=1);

namespace App\Controller;


//require_once("src/view.php");
//require_once('src/database.php');
//require_once('src/Exception/ConfigurationException.php');

use APP\Request;
use APP\Exception\ConfigurationException;
use APP\Exception\NotActivatedException;
use APP\Exception\NotFoundException;
use APP\View;
use APP\Mail\MailHandler;
use APP\Exception\StorageException;
use APP\Exception\WrongCredentialsException;
use APP\Model\NoteModel;
use APP\Model\UserModel;
use APP\Logger\Logger;
use PDO;
use APP\Exception\AppException;
use APP\Exception\EmailException;
use APP\Exception\FileException;

# klasa AbstractController zawiera podstawowe funkcjonalnosci kontrolera w modelu MVC takie jak przypisanie konfiguracji, uruchomienie pozostałych elementów aplikacji oraz przekierowania 
# Jest ona Rozszerzana przez klasę MainController która jest opdowiedzalne za implementacje poszczególnych zadań które nalezą do kontrolera w modelu MVC  
abstract class AbstractController
{
    protected string $defaultAction='searchUser'; 

    protected static array $config=[];

    protected Logger $logger;
    protected Request $request;
    protected View $view;
    protected NoteModel $noteModel;
    protected UserModel $userModel;
    protected MailHandler $mailHandler;

    #funckcja słuzy przypisaniu do klasy konfiguracji kontrolera z pliku config.php 
    public static function initConfiguration(array $config): void
    {
        self::$config=$config; 
       
    }

    # konstruktor klasy inicializuje lub/i przypisuje obiekty klas wykorzystwanych w aplikacji takie jak view odpowidzialne za wyswietlanie HTML, Modele odpowiedzalne za dostep do bazy danych,
    # Request odpowiedzalny za dostep do protokołu HTTP, MailHandler odpowiedzalny za wysyłanie email, Logger odpowiedzialny za zapsiywanie loggów o błedach do pliku
    public function __construct(Request $request,Logger $logger)
    {
        if(empty(self::$config['db']))
        {
            throw new ConfigurationException('Configuration error');
        }

        $this->noteModel=new NoteModel(self::$config['db']);
        $this->userModel=new UserModel(self::$config['db']);
        $this->mailHandler=new MailHandler(self::$config['mail']);
        $this->request=$request;
        $this->view = new View();
        $this->logger=$logger;

    }
    #funkcja run jest funkcja pośrednicząca która ma na celu uruchomienie odpowiednej funkcji kontrolera na podstawie informacji z protokołu HTTP 
    # Zaweiera ona również elementy bezpieczenstwa jak blokowanie dostepu do niektórych funkcji dla nie zalgowanych uzytkowników oraz zabezpieczenie przed Session Hijacking przez pliki cookies
    public function run(): void
    {    
               
   

        try{
            
            #zabazpieczenie przed Session Hijacking poprzez weryfikacje HTTP_USER_AGENT
            if(!empty($this->request->sessionParam('HTTP_USER_AGENT',null)))
            {
                
                if($this->request->serverParam('HTTP_USER_AGENT',null)!==$this->request->sessionParam('HTTP_USER_AGENT',null))
                {
                   
                   
                   session_destroy();
                   $this->redirect('index.php',[]);
                 
                  
                }
            }
            else
            {
                $this->request->setUserAgentSession(($this->request->serverParam('HTTP_USER_AGENT')));
            }




            #W zaleznosci od tego czy jest zalgowany uzytkownik ustawienie domyslej akcji kontrolera
            if($this->request->sessionParam('user_id',null))
            {
                $this->defaultAction='listNotes';
                $this->noteModel->set_user($this->request->sessionParam('user_id',null));
            }


            #wywołanie odpowiednej akcji w zaleznosci od parametrów otrzymanych przez protokół HTTP
            $action=$this->action().'Action';
            if(!method_exists($this,$action))
            {
                $action=$this->defaultAction.'Action';   
            }
            #ogranczenie dostepu do funkcji które sa dedykowane tylko dla zalogowanych uzytkowników
            if(!$this->request->sessionParam('user_id',null))
            {
                if($action!=='searchUserAction' && $action!=='createUserAction' && $action!=='termsAction' && $action!=='activateUserAction' && $action!=="chgPasswdAction")
                {
                    $this->redirect('index.php',['error'=>'missingUser']);
                }
            }
            $this->$action();
            
            
        # przechwytywanie wyjątków powstałych w czasie działania funkcji run
        }catch(StorageException $e)
        {
            $this->logger->writeLogEntry($e->getMessage());
            
            $this->view->render('error',['message'=>$e->getMessage()]);
            
        
        }
        catch(NotFoundException $e)
        {
            $this->logger->writeLogEntry($e->getMessage());
    
            $this->redirect('index.php',['error'=>'NotFound']);
           
        }
        catch(WrongCredentialsException $e)
        {
            $this->logger->writeLogEntry($e->getMessage());
           
            $this->redirect('index.php',['error'=>'WrongCredentials']);
            
        }
        catch(NotActivatedException $e)
        {
            $this->logger->writeLogEntry($e->getMessage());
            $this->redirect('index.php',['error'=>'NotActivated']);
        }
       
    }

    # funkckja jest odpowiedzalana za przekierowania na odpowienie adresy przy uzyciu wbudowanej funkcji header
    final protected function redirect(string $to, array $params):void
    {
        $location=$to;
        $queryParams=[];
        if(count($params))
        {
            foreach($params as $key => $value)
            {
                $queryParams[]=urlencode($key).'='.urlencode($value);
    
            }
            $queryParams=implode('&',$queryParams);
            $location.='?'.$queryParams;
        }
    
        header("Location: $location");
        exit();
    }
    

    #funckcja action zwraca odpowiednia nazwe metody w zaleznosci od parametrów przesłanych przez protokół HTTP
    private function action(): string
    {
        $action=$this->request->getParam('action',$this->defaultAction);
       
        return $action?? $this->defaultAction;
    }

}