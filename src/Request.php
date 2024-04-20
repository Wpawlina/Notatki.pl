<?php
declare(strict_types=1);

namespace App;



# klasa request jest odpowiedzalna za obsługę protokołu HTTP poprzez dostep to odpowiednich tablic z parametrami protokołu takimi jak $_GET,$_POST,$_SESSION,$_SERVER 
class Request
{
    private array  $get = [];
    private array  $post = [];
    private array  $server=[];
    private array  $session=[];

    #konstruktor przypisuje odpowiednie tablice paramterów protokołu to własciwosci klasy Request 
    public function __construct(array $get, array $post, array $server,array & $session)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server=$server;
        $this->session=&$session;
    }

    #metoda isPost sprawdza czy ostatnie rzadanie jest typu POST
     #metoda isPost sprawdza czy ostatnie rzadanie jest typu POST
    public function isPost() :bool
    {
        return $this->server['REQUEST_METHOD'] === "POST";

    }
    #metoda isPost sprawdza czy ostatnie rzadanie jest typu GET
    public function isGet() :bool
    {
        return $this->server['REQUEST_METHOD'] === "GET";

    }
    #metoda sprawdza czy tablica post jest nie pusta 
    public function hasPost():bool
    {
        return !empty($this->post);
    }
    #metoda zwraca parametr z tablicy $_GET o podanej nazwie
    public function getParam(string $name, $default=null) 
    {
        return $this->get[$name] ?? $default;

    }
    #metoda zwraca parametr z tablicy $_POST o podanej nazwie
    public function postParam(string $name,$default=null)
    {
        return $this->post[$name] ?? $default;
    }
    #metoda zwraca parametr z tablicy $_SESSION o podanej nazwie
    public function sessionParam(string $name,$default=null)
    {
        return $this->session[$name]?? $default;
    }
    #metoda zwraca parametr z tablicy $_SERVER o podanej nazwie
    public function serverParam(string $name,$default=null)
    {
        return $this->server[$name]?? $default;
    }
    #metoda ustawia id uzytkownika i jego login w sesji po zalogowaniu
    public function setUserSession(int $user_id,string $login):void
    {
       $this->session['user_id']=$user_id;
       $this->session['login']=$login;
       session_regenerate_id();
       
    }
    #metoda zapisuje nazwe agenta uzytkownika w sesji po zalogowaniu uzytkownika w celu zabespieczenia przed Session Hijacking
    public function setUserAgentSession(string $value):void
    {
       $this->session['HTTP_USER_AGENT']=$value;
       session_regenerate_id();
       
    }
    #usuwa informacje o uzytkowniku z sesji po wylogowaniu uzytkownika
    public function unsetUserSession():void
    {
        unset($this->session['user_id']);
        unset($this->session['login']);
    }

}