<?php
declare(strict_types=1);

namespace App;

# [PL] klasa request jest odpowiedzalna za obsługę protokołu HTTP poprzez dostep to odpowiednich tablic z parametrami protokołu takimi jak $_GET,$_POST,$_SESSION,$_SERVER 
# [ENG] The Request class is responsible for handling the HTTP protocol by accessing the appropriate arrays with protocol parameters such as $_GET, $_POST, $_SESSION, $_SERVER
class Request
{
    private array $get = [];
    private array $post = [];
    private array $server = [];
    private array $session = [];

    # [PL] konstruktor przypisuje odpowiednie tablice paramterów protokołu to własciwosci klasy Request 
    # [ENG] The constructor assigns the appropriate protocol parameter arrays to the properties of the Request class
    public function __construct(array $get, array $post, array $server, array &$session)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->session = &$session;
    }

    # [PL] metoda isPost sprawdza czy ostatnie rzadanie jest typu POST
    # [ENG] The isPost method checks if the last request is of type POST
    public function isPost(): bool
    {
        return $this->server['REQUEST_METHOD'] === "POST";
    }

    # [PL] metoda isGet sprawdza czy ostatnie rzadanie jest typu GET
    # [ENG] The isGet method checks if the last request is of type GET
    public function isGet(): bool
    {
        return $this->server['REQUEST_METHOD'] === "GET";
    }

    # [PL] metoda hasPost sprawdza czy tablica post jest nie pusta 
    # [ENG] The hasPost method checks if the post array is not empty
    public function hasPost(): bool
    {
        return !empty($this->post);
    }

    # [PL] metoda zwraca parametr z tablicy $_GET o podanej nazwie
    # [ENG] The getParam method returns a parameter from the $_GET array with the given name
    public function getParam(string $name, $default = null)
    {
        return $this->get[$name] ?? $default;
    }

    # [PL] metoda zwraca parametr z tablicy $_POST o podanej nazwie
    # [ENG] The postParam method returns a parameter from the $_POST array with the given name
    public function postParam(string $name, $default = null)
    {
        return $this->post[$name] ?? $default;
    }

    # [PL] metoda zwraca parametr z tablicy $_SESSION o podanej nazwie
    # [ENG] The sessionParam method returns a parameter from the $_SESSION array with the given name
    public function sessionParam(string $name, $default = null)
    {
        return $this->session[$name] ?? $default;
    }

    # [PL] metoda zwraca parametr z tablicy $_SERVER o podanej nazwie
    # [ENG] The serverParam method returns a parameter from the $_SERVER array with the given name
    public function serverParam(string $name, $default = null)
    {
        return $this->server[$name] ?? $default;
    }

    # [PL] metoda ustawia id uzytkownika i jego login w sesji po zalogowaniu
    # [ENG] The setUserSession method sets the user ID and login in the session after logging in
    public function setUserSession(int $user_id, string $login): void
    {
        $this->session['user_id'] = $user_id;
        $this->session['login'] = $login;
        session_regenerate_id();
    }

    # [PL] metoda zapisuje nazwe agenta uzytkownika w sesji po zalogowaniu uzytkownika w celu zabespieczenia przed Session Hijacking
    # [ENG] The setUserAgentSession method saves the user agent name in the session after logging in to protect against Session Hijacking
    public function setUserAgentSession(string $value): void
    {
        $this->session['HTTP_USER_AGENT'] = $value;
        session_regenerate_id();
    }

    # [PL] usuwa informacje o uzytkowniku z sesji po wylogowaniu uzytkownika
    # [ENG] The unsetUserSession method removes user information from the session after logging out
    public function unsetUserSession(): void
    {
        unset($this->session['user_id']);
        unset($this->session['login']);
    }
}