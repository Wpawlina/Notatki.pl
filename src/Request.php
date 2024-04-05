<?php
declare(strict_types=1);

namespace App;

class Request
{
    private array  $get = [];
    private array  $post = [];
    private array  $server=[];
    private array  $session=[];

    public function __construct(array $get, array $post, array $server,array & $session)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server=$server;
        $this->session=&$session;
    }
    public function isPost() :bool
    {
        return $this->server['REQUEST_METHOD'] === "POST";

    }

    public function isGet() :bool
    {
        return $this->server['REQUEST_METHOD'] === "GET";

    }
    public function hasPost():bool
    {
        return !empty($this->post);
    }
    public function getParam(string $name, $default=null) 
    {
        return $this->get[$name] ?? $default;

    }
    public function postParam(string $name,$default=null)
    {
        return $this->post[$name] ?? $default;
    }
    public function sessionParam(string $name,$default=null)
    {
        return $this->session[$name]?? $default;
    }
    public function serverParam(string $name,$default=null)
    {
        return $this->server[$name]?? $default;
    }

    public function setUserSession(int $user_id,string $login):void
    {
       $this->session['user_id']=$user_id;
       $this->session['login']=$login;
       session_regenerate_id();
       
    }
    public function setUserAgentSession(string $value):void
    {
       $this->session['HTTP_USER_AGENT']=$value;
       session_regenerate_id();
       
    }
    public function unsetUserSession():void
    {
        unset($this->session['user_id']);
    }

}