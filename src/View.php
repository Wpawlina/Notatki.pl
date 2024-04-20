<?php

declare(strict_types=1);

namespace App;

#klasa View jest odpowiedzalna za wyswietlanie dopowiedniego tamplatu dla danego modułu aplikacji
class View
{
    #metoda render wyswietla dany tamplate w zaleznosci od parametru page który otrzyma od kontrolera oraz od dodatkowych parametrów zawartych w tablicy params
    public function render(string $page, array $params=[]): void
    {
       
        $params=$this->escape($params);
       
        require_once('templates/layout.php');
    }
    #metoda escape zabespiecza aplikacje przedtym wyswietleniem niebezpiecznych treści 
    # zabespiecznie polega na eskajpowaniu w zależnosci od typu parametru
    # jest to zabezpieczenie przed HTML injection attack
    private function escape(array $params):array
    {
        $clearParams=[];
        foreach($params as $key=> $param)
        {

            switch(true)
            {
                case is_array($param):
                    $clearParams[$key]=$this->escape($param);
                break;
                case is_int($param):
                    $clearParams[$key]=$param;
                break;
                case is_string($param):
                    $clearParams[$key]=htmlentities($param);
                break;
                default:
                    $clearParams[$key]=$param;
                break;
            }
        }
        return $clearParams;
    }

}