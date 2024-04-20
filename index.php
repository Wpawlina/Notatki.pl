<?php
declare(strict_types=1);

session_start();


# wbudowana funkcja spl_autoload_register służy do automatycznego ładowania plików z Klasami, wyszukuje odpowiedni plik po nazwie wywołanej klasy poprzez dostosowanie sciezki dostepu do pliku na jej podstawie
spl_autoload_register(function(string $classNamespace){
  
  //dump($classNamespace);
   
    $path=str_replace(['\\','APP/'],['/',''],$classNamespace);
    $path='src/'.$path.'.php';
    require_once($path);
});






require_once("src/utils/debug.php");

//require_once("src/Controller/NoteController.php");

//require_once("src/request.php");


$config=require_once("config/config.php");

//require_once('src/Exception/AppException.php');
use APP\Exception\AppException;
use APP\Exception\ConfigurationException;
//use Throwable;
use APP\Request;
use APP\Controller\AbstractController;


use APP\Controller\MainController;
use APP\Exception\EmailException;
use APP\Logger\Logger;

$request=new Request($_GET,$_POST,$_SERVER,$_SESSION);
$logger=new Logger($config['file']);

#inicjalizacja i wywołanie kontrolera
try 
{
    AbstractController::initConfiguration($config);
 (new MainController($request,$logger))->run();
 

}
#przechwytywanie wyjątków które uniemożliwiaja dalsze działanie aplikacji
catch(ConfigurationException $e)
{
    echo "<h1>Wystąpił błąd w aplikacji</h1>";
    echo '<h3> Problem z konfiguracją proszę skontaktowac sie z adminsitracją</h3>';
    dump($e);
    $logger->writeLogEntry($e->getMessage());
}
catch(EmailException $e)
{
    echo "<h1>Wystąpił błąd w wysyłania maila</h1>";
    dump($e);
    $logger->writeLogEntry($e->getMessage());
}
catch(AppException $e)
{
    echo "<h1>Wystąpił błąd w aplikacji</h1>";
    echo '<h3> Sczegóły '.$e->getMessage().'</h3>';
    dump($e);
    $logger->writeLogEntry($e->getMessage());
    //echo '<h3> Sczegóły '.$e->getPrevious()->getMessage().'</h3>';
  

}
catch(Throwable $e)
{
    dump($e);
   echo "<h1>Wystąpił błąd w aplikacji</h1>";
   $logger->writeLogEntry($e->getMessage());
}








