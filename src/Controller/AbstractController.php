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
use PDO;

abstract class AbstractController
{
    protected string $defaultAction='searchUser'; 

    protected static array $config=[];

    protected Request $request;
    protected View $view;
    protected NoteModel $noteModel;
    protected UserModel $userModel;
    protected MailHandler $mailHandler;

    public static function initConfiguration(array $config): void
    {
        self::$config=$config; 
       
    }


    public function __construct(Request $request)
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


    }
    public function run(): void
    {    
               
   

        try{
            
      
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

            if($this->request->sessionParam('user_id',null))
            {
                $this->defaultAction='listNotes';
                $this->noteModel->set_user($this->request->sessionParam('user_id',null));
            }
            $action=$this->action().'Action';
            if(!method_exists($this,$action))
            {
                $action=$this->defaultAction.'Action';   
            }
            if(!$this->request->sessionParam('user_id',null))
            {
                if($action!=='searchUserAction' && $action!=='createUserAction' && $action!=='termsAction' && $action!=='activateUserAction' && $action!=="chgPasswdAction")
                {
                    $this->redirect('index.php',['error'=>'missingUser']);
                }
            }
       
            
           
            $this->$action();
            
            

        }catch(StorageException $e)
        {
            $this->view->render('error',['message'=>$e->getMessage()]);
            dump($e);
        
        }
        catch(NotFoundException $e)
        {
            
            $this->redirect('index.php',['error'=>'NotFound']);
            
        }
        catch(WrongCredentialsException $e)
        {
            $this->redirect('index.php',['error'=>'WrongCredentials']);
        }
        catch(NotActivatedException $e)
        {
            $this->redirect('index.php',['error'=>'NotActivated']);
        }
       
    }
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
   
     private function action(): string
    {
        $action=$this->request->getParam('action',$this->defaultAction);
       
        return $action?? $this->defaultAction;
    }
    /*private function generatePepper(int $length=10): string
    {
            $result='';
            for($i=0;$i<$length;$i++)
            {
                $result.=chr(rand(33,126));
            }
            return $result;
    }
  */

}