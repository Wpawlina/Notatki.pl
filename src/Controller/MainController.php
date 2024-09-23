<?php

declare(strict_types=1);

namespace App\Controller;

use APP\Request;
use APP\Exception\ConfigurationException;
use APP\Exception\NotActivatedException;
use APP\Exception\NotFoundException;
use APP\Exception\WrongCredentialsException;
use DateTime;
use PDO;

//require_once('src/Controller/AbstractController.php');

# [PL] klasa MainController dziedziczy funkcjonalnosci zawarte w AbstractController i rozszerze ja o metody wywoływane dla opowiednich parametrów action przesyłanych przez protokół HTTP
# [ENG] The MainController class inherits functionalities from AbstractController and extends it with methods called for corresponding action parameters sent via HTTP protocol
class MainController extends AbstractController
{
    
    private const PAGE_SIZE=10;
  
    # [PL] metoda wywołujaca odpowiedni moduł do stworzenia nowej notatki przez uzytkownika
    # [PL] pobiera poprzez metode POST informacje z formularza i zapisuje je w bazie danych 
    # [ENG] Method that invokes the appropriate module to create a new note by the user
    # [ENG] It receives information from the form via POST method and saves it in the database
    protected function createNoteAction():void
    {           
        if($this->request->hasPost())
        {
            $noteData=['title'=>$this->request->postParam('title'),'description'=>$this->request->postParam('description')];
            $this->noteModel->create($noteData);
            $this->redirect('index.php',['before'=>'created']);   
        }
        $this->view->render('createNote');

    }
    # [Pl] metoda wywołujaca odpowiedni moduł do wyswietlenia szczegółów odpowiedniej notatki 
    # [ENG] Method that invokes the appropriate module to display details of a specific note
    protected function showNoteAction():void
    {
        
        $this->view->render('showNote',['note'=>$this->getNote()]);
    }

    # [PL] metoda wywołujaca odpowiedni moduł do wylistowania notatek przez uzytkownika
    # w zaleznosci od parametrów przesłanych w formularzu wyszukuje w bazie danych notatki spełnajace podane przez uzytkownika warunki i wyswietla je w preferowany przez uzytkownika sposób
    # [ENG] Method that invokes the appropriate module to list notes for the user
    #  Depending on the parameters sent in the form, it searches the database for notes meeting the conditions specified by the user and displays them in the user's preferred way
    protected function listNotesAction():void
    {
        $title=$this->request->getParam('title');
        $date=$this->request->getParam('date');
        $phrase=[];
        if($date)
        {
            $phrase['date']=$date;
        }
        if($title)
        {
            $phrase['title']=$title;
        }
       

        $pageSize=(int) $this->request->getParam('pageSize',self::PAGE_SIZE);
        $pageNumber=(int) $this->request->getParam('page',1);
        if(!(in_array($pageSize,[1,5,10,25]))){
            $pageSize=self::PAGE_SIZE;
        }
        $sortBy=$this->request->getParam('sortby','title');
        $sortOrder=$this->request->getParam('sortorder','desc');
        if($phrase)
        {
            $noteList=$this->noteModel->search($phrase,$pageNumber,$pageSize,$sortBy,$sortOrder);
            $notes=$this->noteModel->searchCount($phrase);
        }
        else
        {
            
            $noteList=$this->noteModel->list($pageNumber,$pageSize,$sortBy,$sortOrder);
            $notes=$this->noteModel->count();
        }
        

        $viewParams=[
            'page'=>[
                'number'=>$pageNumber,
                'size'=>$pageSize,
                'pages'=>(int)ceil($notes/$pageSize)
            ],
            'where'=>[
                'title'=>$title,
                'date'=>$date,
            ],
        
            'sort'=>[
                'by'=>$sortBy,
                'order'=>$sortOrder,
            ],
            'before'=>$this->request->getParam('before'),
            'error'=>$this->request->getParam('error'),
            'notes'=>$noteList   
        ];
     
     
        $this->view->render('listNote',$viewParams ?? []);        
    }

    # [PL] metoda wywołujaca odpowiedni moduł do edycji notatki przez uzytkownika
    #pobiera inforamcje od uzytkownika poprzez formularz i metode POST a nastepnie modyfikuje dane w bazie danych
    # [ENG] Method that invokes the appropriate module for editing a note by the user
    #  It receives information from the user via a form and POST method, then modifies the data in the database
    protected function editNoteAction():void
    {
        
        if($this->request->isPost())
        {
            $noteId=(int)$this->request->postParam('id');
            $noteData=['title'=>$this->request->postParam('title'),'description'=>$this->request->postParam('description')];
            $this->noteModel->edit($noteId,$noteData);
            $this->redirect('index.php',['before'=>'edited']);
        }
       $this->view->render('editNote',['note'=>$this->getNote()]);
    }
    
    # [PL] metoda wywołujaca odpowiedni moduł do usuniecia notatki przez uzytkownika
    #poprzez formularz sprawdza czy uzytkownik napewno chce usunac te notatke jesli tak to usuwa informacje o danej notatce z bazy danych
    # [ENG] Method that invokes the appropriate module for deleting a note by the user
    # It checks via a form if the user really wants to delete this note, if so, it removes the information about the given note from the database

    protected function deleteNoteAction():void
    {
        if($this->request->isPost())
        {
          $noteId=(int)$this->request->postParam('id');
          $this->noteModel->delete($noteId);
          $this->redirect('index.php',['before'=>'deleted']);
        }
        $this->view->render('deleteNote',['note'=>$this->getNote()]);
       
    }

    # [PL] metoda wywołujaca odpowiedni moduł do logowania uzytkownika
    #pobiera inforamcje od uzytkownika poprzez formularz i metode POST a nastepnie weryfkuje ich poprawnosc w stosunku do danych znajdujacych sie w bazie danych
    #jesli dane sa poprawne zapisuje dane o uzytkowniku w sesji
    # [ENG] Method that invokes the appropriate module for user login
    #  It receives information from the user via a form and POST method, then verifies their correctness against the data in the database
    # If the data is correct, it saves the user's data in the session

    protected function searchUserAction():void
    {
        
        if(!$this->request->sessionParam('user_id',null))
        {
            if($this->request->isPost())
            {
              $login=$this->request->postParam('login','');
              $password=$this->request->postParam('password','');
              $result=$this->userModel->search($login);
              if(!isset($result['password']))
              {
                throw new NotFoundException("nie znaleziono uzytkownia o loginie $login");
              }
              if($result['activated']===0)
              {
                throw new NotActivatedException("Konto o loginie $login nie aktywne");
              }
              $password=$password.self::$config['password']['pepper'];
              if(password_verify($password,$result['password']))
              {
                $result['user_id']=(int) $result['user_id'];
                $this->request->setUserSession( $result['user_id'],$login);
                $this->redirect('index.php',['before'=>'loggedin']);
              }
              else
              {
                throw new WrongCredentialsException(" Błędne dane logowania $login");
              }
              
               
                
            }
              
            $viewParams=[
              
                'error'=>$this->request->getParam('error'),
                'before'=>$this->request->getParam('before'),
                  
            ];
            
            $this->view->render('searchUser',$viewParams);
        }
        else
        {
            $this->redirect('index.php?action=showUser',[]);

        }
       

    }




    # [PL] metoda wywołujaca odpowiedni moduł do utwozenia odpowiedniego uzytkownika
    #pobiera inforamcje od uzytkownika poprzez formularz i metode POST a nastepnie werfikuje poprawnosc dancyh przesłanych przez uzytkownika 
    # jesli dane sa poprawne dodaje nowy rekord w bazie danych i wysyła kod aktywacyjny na podany email
    # hasła sa zabezpieczane poprzez wbudowana metode password_hash i algorytm BCRYPT oraz dodatkowy kod dodawany do hasła tzw Pepper 
    # [ENG] Method that invokes the appropriate module to create a new user
    #  It receives information from the user via a form and POST method, then verifies the correctness of the data sent by the user
    #  If the data is correct, it adds a new record to the database and sends an activation code to the provided email
    #  Passwords are secured using the built-in password_hash method and BCRYPT algorithm, plus an additional code added to the password called Pepper

    protected function createUserAction():void
    {
        $viewParams=[
            'error'=>[],
            'before'=>[]
        ];
        if($this->request->isPost())
        {
          $validation=true;
          $login=$this->request->postParam('login','');
          $firstName=$this->request->postParam('firstName','');
          $lastName=$this->request->postParam('lastName','');
          $password=$this->request->postParam('password','');
          $password2=$this->request->postParam('password2','');
          $terms=$this->request->postParam('terms','');
        
          $recaptchaSecretKey = self::$config['recaptcha']['secretKey']; 
          $recaptchaResponse =$this->request->postParam('recaptcha-response','');
          
          //validation

          //email(login)
          $loginSanitezed=filter_var($login,FILTER_SANITIZE_EMAIL);
          if($login!==$loginSanitezed || filter_var($login,FILTER_VALIDATE_EMAIL)===false)
          {
            $viewParams['error']['login']=1;
            $validation=false;
          }
          else
          {
            $checkEmail=$this->userModel->list($login);
            if($checkEmail!==0)
            {
            $viewParams['error']['login']=2;
            $validation=false;
            }
          }
         
          //first name
          if(strlen($firstName)>30)
          {
            $viewParams['error']['firstName']=1;
            $validation=false;
          }
          //last name
          if(strlen($firstName)>30)
          {
            $viewParams['error']['lastName']=1;
            $validation=false;
          }
          //password
          if($password!==$password2)
          {
            $viewParams['error']['password2']=1;
            $validation=false;
          }
          $pattern = '/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%^&+=!])[A-Za-z\d@#$%^&+=!]{8,30}$/';// 8-30 characters atleast one digit,capitalized letter and special character
          if (!preg_match($pattern, $password)) 
          {
            $viewParams['error']['password']=1;
            $validation=false;
          } 
          //terms
          if(!$terms)
          {
            $viewParams['error']['terms']=1;
            $validation=false;
          }
          
        
          $url = 'https://www.google.com/recaptcha/api/siteverify';
          $data = [
            'secret' => $recaptchaSecretKey,
            'response' => $recaptchaResponse,
          ];
          $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
          ]; 
          $context = stream_context_create($options);
          $response = file_get_contents($url, false, $context);
          $result = json_decode($response, true);
          if (!$result['success'])
           {
                $validation=false;
                $viewParams['error']['recaptcha']=1;
           }
           
           if($validation===true)
           {
            $pepper=self::$config['password']['pepper'];
            $password=$password.$pepper;
            $password=password_hash($password,PASSWORD_BCRYPT);
            $code=$this->generateRandomString();
            $this->userModel->create($login,$firstName,$lastName,$password,$code);
            $this->mailHandler->create($login,$code);
            $this->redirect('index.php',['before'=>'userCreated']);
           } 
           //false validation;
           $viewParams['before']=[
            'login'=>$login,
            'firstName'=>$firstName,
            'lastName'=>$lastName,
            'terms'=>$terms
           ]; 
        }
        $this->view->render('createUser',$viewParams);
    }


    # [PL] metoda wywołujaca odpowiedni moduł do wyswietlenia informacji o zalogowanym uzytkowniku
     # [ENG] Method that invokes the appropriate module to display information about the logged-in user
    protected function showUserAction():void
    {
        $result=$this->userModel->show($this->request->sessionParam('user_id',null));
        $viewParams['user']=$result;
        $this->view->render('showUser',$viewParams);

    }

    # [PL] metoda wywołujaca odpowiedni moduł do wylogowania uzytkownika poprzez usuniecie informacji o nim z sesji
    # [ENG] Method that invokes the appropriate module to display information about the logged-in user

    protected function logoutAction():void
    {
        $this->request->unsetUserSession();
        $this->redirect('index.php',['before'=>'loggedout']);

    }

    # [PL] metoda wywołujaca odpowiedni moduł do edycji infomacji o  uzytkowniku 
    # pobiera inforamcje o uzytkowniku poprzez formularz a nastepnie aktualizuje informacje w bazie danych
    # [ENG] Method that invokes the appropriate module to edit user information
    # It receives user information via a form and then updates the information in the database

    protected function editUserAction():void
    {
        if($this->request->isPost())
        {
            $firstName=$this->request->postParam('firstName');
            $lastName=$this->request->postParam('lastName');
            $this->userModel->edit($this->request->sessionParam('user_id',null),$firstName,$lastName);
            $this->redirect('index.php',['before'=>'userEdited']);
        }
        $result=$this->userModel->show($this->request->sessionParam('user_id',null));
        $viewParams['user']=$result;
        $this->view->render('editUser',$viewParams);
        
    }



   # [PL] metoda wywołujaca odpowiedni moduł do zamkniecia konta uzytkownika poprzez usuniecie informacji o nim z bazy danych 
   # [ENG] Method that invokes the appropriate module to close a user's account by removing their information from the database

   protected function deleteUserAction():void
   {
    if($this->request->isPost())
    {
        $this->userModel->delete($this->request->sessionParam('user_id',null));
        $this->request->unsetUserSession();
        $this->redirect('index.php',['before'=>'deletedUser']);
    }
    $this->view->render('deleteUser',[]); 
   }



   # [PL] metoda wywołujaca odpowiedni moduł do aktywacji konta uzytkownika poprzez kod aktywacyny wysłany przy utworzeniu konta na emial uzytkownika
   # jesli kod jest poprawny i nie upłynoł czas na aktywacje konta, konto zostaje aktywowane i uzytkownik może sie juz zalogować
    # [ENG]  Method that invokes the appropriate module to activate a user's account via the activation code sent to the user's email upon account creation
    # If the code is correct and the activation time has not expired, the account is activated and the user can now log in


   protected function activateUserAction() : void
   {
    
    $actCode=$this->request->getParam('actCode');
    $curTime=new DateTime();
    $result=$this->userModel->getCodeInfo($actCode);
    
    if(count($result)===0)
    {
        $this->view->render('activateUser',['result'=>'Negative']);

    }
    else
    {
        $time=DateTime::createFromFormat('Y-m-d H:i:s',$result['expiry']);
        if( $curTime<=$time and $actCode===$result['code'])
        {
            $this->userModel->activate($result['user_id']);
            $this->userModel->deleteUsedCode($actCode);
            $this->view->render('activateUser',['result'=>'Positive']);
        }
        else
        {
            $this->view->render('activateUser',['result'=>'Negative']);
        }
    } 
   }


   #metoda wywołujaca odpowiedni moduł do zmiany hasła przez uzytkownika
   # [PL] jesli  akcja jest wywołana z poziomu zalogowanego uzytkownika poprzez formularz uzytkownik podaje nowe hasło i jest ono aktualizowane w bazie danych
   # jesli akcja jest wywołana z poziomu niezalogowanego uzytkownika poprzez opcje zresetuj hasło na emaila uzytkownika wysyłany jest link z kodem do resetu hasła 
   # akcja jest wywołana z poziomu linku z kodem do zmiany o ile kod jest poprawny uzytkownik podaje nowe hasło poprzez formularz i jest ono aktualizowane w bazie danych
   # [ENG] Method that invokes the appropriate module for changing the user's password
    # If the action is called from a logged-in user via a form, the user provides a new password and it is updated in the database
    # If the action is called from a non-logged-in user via the reset password option, a link with a password reset code is sent to the user's email
    # If the action is called from a link with a code, and if the code is correct, the user provides a new password via a form and it is updated in the database

   protected function chgPasswdAction():void
   {
        $userId=$this->request->sessionParam('user_id',null);
        

        if($userId!==null)
        {
            $login=$this->request->sessionParam('login',null);
            $viewParams['before']='WebsiteLogged';
            if($this->request->isPost())
            {
                $password=$this->request->postParam('password','');
                $password2=$this->request->postParam('password2','');
                $validation=true;
                if($password!==$password2)
                {
                    $viewParams['error']['password2']=1;
                    $validation=false;
                }
                $pattern = '/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%^&+=!])[A-Za-z\d@#$%^&+=!]{8,30}$/';// 8-30 characters atleast one digit,capitalized letter and special character
                if (!preg_match($pattern, $password)) 
                {
                    $viewParams['error']['password']=1;
                    $validation=false;
                }
                if($validation===true)
                {
                    $pepper=self::$config['password']['pepper'];
                    $password=$password.$pepper;
                    $password=password_hash($password,PASSWORD_BCRYPT);
                    $this->userModel->updatePassword($userId,$password);
                    $this->mailHandler->notifyPassword($login);
                    $this->redirect('index.php',['before'=>'ChangedPassword']);
                }
                
            }

            $this->view->render('changePasswd',$viewParams);
        }
        else 
        {
            $code=$this->request->getParam('chgCode');
            if($code!==null)
            {
                $viewParams['before']='Email';
                $viewParams['code']=$code;
                $result=$this->userModel->getCodeInfo($code);
                $curTime=new DateTime();
                $time=DateTime::createFromFormat('Y-m-d H:i:s',$result['expiry']);
                $userId=$result['user_id'];
                if($code===$result['code'] and $curTime<$time)
                {
                    if($this->request->isPost())
                    {
                        $password=$this->request->postParam('password','');
                        $password2=$this->request->postParam('password2','');
                        $validation=true;
                        if($password!==$password2)
                        {
                            $viewParams['error']['password2']=1;
                            $validation=false;
                        }
                        $pattern = '/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%^&+=!])[A-Za-z\d@#$%^&+=!]{8,30}$/';// 8-30 characters atleast one digit,capitalized letter and special character
                        if (!preg_match($pattern, $password)) 
                        {
                            $viewParams['error']['password']=1;
                            $validation=false;
                        }
                        if($validation===true)
                        {
                            $pepper=self::$config['password']['pepper'];
                            $password=$password.$pepper;
                            $password=password_hash($password,PASSWORD_BCRYPT);
                            $login=$this->userModel->getEmailFromUserId($userId);
                            $this->userModel->deleteUsedCode($code);
                            $this->userModel->updatePassword($userId,$password);
                            $this->mailHandler->notifyPassword($login);
                            $this->redirect('index.php',['before'=>'ChangedPassword']);
                        }
                    }
                    $this->view->render('changePasswd',$viewParams);
                }
                else
                {
                    throw new WrongCredentialsException("Błędne dane logowania kod:$code id:$userId ");
                }
            }
            else
            {
                $viewParams['before']='WebsiteNotLogged';
                if($this->request->isPost())
                {

                    $login=$this->request->postParam('login','');
                    $checkEmail=$this->userModel->list($login);
                    $validation=true;
                    
                    $recaptchaSecretKey=self::$config['recaptcha']['secretKey']; 
                    $recaptchaResponse =$this->request->postParam('recaptcha-response','');
                    
                    if($checkEmail===0)
                    {
                        $viewParams['error']['login']=1; 
                        $validation=false;
                    }
                    
                    $url = 'https://www.google.com/recaptcha/api/siteverify';
                    $data = [
                      'secret' => $recaptchaSecretKey,
                      'response' => $recaptchaResponse,
                    ];
                    $options = [
                      'http' => [
                          'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                          'method' => 'POST',
                          'content' => http_build_query($data),
                      ],
                    ]; 
                    $context = stream_context_create($options);
                    $response = file_get_contents($url, false, $context);
                    $result = json_decode($response, true);
                    if (!$result['success'])
                     {
                          $validation=false;
                          $viewParams['error']['recaptcha']=1;
                     }
                    
                    if($validation==true)
                    {
                        $chgCode=$this->generateRandomString();
                        $this->mailHandler->chgPasswd($login,$chgCode);
                        $this->userModel->insertChgPasswdCode($login,$chgCode);
                        $this->redirect("index.php",["before"=>"emailSent"]);
                    }
                }
                $this->view->render('changePasswd',$viewParams);

            }
        }

   }
   

   # [PL] metoda wywołujaca odpowiedni moduł do wyswietlenia regulaminu serwisu
   # [ENG] Method that invokes the appropriate module to display the website's terms of service
   protected function termsAction():void
   {
        $this->view->render('terms');

   }
    

   # [PL] metoda odpwoeidzalna za uzyskanie informacji o odowiedniej notatce z bazy danych
   # [ENG] Method responsible for obtaining information about a specific note from the database
    private function getNote():array
    {
        $noteId=(int)$this->request->getParam('id');
        if(!$noteId)
        {
            $this->redirect('index.php',['error'=>'missingNoteId']);
            
        }
        $note=$this->noteModel->get($noteId);
        return $note;
    }

    # [PL] funkcja jest opowiedzalana za generowanie kodów aktywacji i zmiany hasła
    # [ENG] Function responsible for generating activation and password change codes

    private function generateRandomString($length = 64):string 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
   

  
}