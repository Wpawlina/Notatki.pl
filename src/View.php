<?php

declare(strict_types=1);

namespace App;

# [PL] klasa View jest odpowiedzalna za wyswietlanie dopowiedniego tamplatu dla danego modułu aplikacji
# [ENG] The View class is responsible for displaying the appropriate template for a given application module
class View
{
    # [PL] metoda render wyswietla dany tamplate w zaleznosci od parametru page który otrzyma od kontrolera oraz od dodatkowych parametrów zawartych w tablicy params
    # [ENG] The render method displays a given template depending on the page parameter received from the controller and additional parameters contained in the params array
    public function render(string $page, array $params=[]): void
    {
        $params = $this->escape($params);
        require_once('templates/layout.php');
    }

    # [PL] metoda escape zabespiecza aplikacje przedtym wyswietleniem niebezpiecznych treści 
    #  zabespiecznie polega na eskajpowaniu w zależnosci od typu parametru
    #  jest to zabezpieczenie przed HTML injection attack
    # [ENG] The escape method protects the application from displaying dangerous content
    #  The protection involves escaping depending on the type of parameter
    #  This is a protection against HTML injection attacks
    private function escape(array $params): array
    {
        $clearParams = [];
        foreach ($params as $key => $param)
        {
            switch (true)
            {
                case is_array($param):
                    $clearParams[$key] = $this->escape($param);
                    break;
                case is_int($param):
                    $clearParams[$key] = $param;
                    break;
                case is_string($param):
                    $clearParams[$key] = htmlentities($param);
                    break;
                default:
                    $clearParams[$key] = $param;
                    break;
            }
        }
        return $clearParams;
    }
}