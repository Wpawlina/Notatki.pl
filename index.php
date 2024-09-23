<?php
declare(strict_types=1);

session_start();

# [PL] wbudowana funkcja spl_autoload_register służy do automatycznego ładowania plików z Klasami, wyszukuje odpowiedni plik po nazwie wywołanej klasy poprzez dostosowanie sciezki dostepu do pliku na jej podstawie
# [ENG] The built-in function spl_autoload_register is used for automatically loading class files, it searches for the appropriate file by the name of the called class by adjusting the file path based on it
spl_autoload_register(function(string $classNamespace){
  
  //dump($classNamespace);
   
    $path=str_replace(['\\','APP/'],['/',''],$classNamespace);
    $path='src/'.$path.'.php';
    require_once($path);
});

require_once("src/utils/debug.php");

//require_once("src/Controller/NoteController.php");

//require_once("src/request.php");

$config = require_once("config/config.php");

//require_once('src/Exception/AppException.php');
use APP\Exception\AppException;
use APP\Exception\ConfigurationException;
//use Throwable;
use APP\Request;
use APP\Controller\AbstractController;

use APP\Controller\MainController;
use APP\Exception\EmailException;
use APP\Logger\Logger;

$request = new Request($_GET, $_POST, $_SERVER, $_SESSION);
$logger = new Logger($config['file']);

# [PL] inicjalizacja i wywołanie kontrolera
# [ENG] initialization and invocation of the controller
try 
{
    AbstractController::initConfiguration($config);
    (new MainController($request, $logger))->run();
}
# [PL] przechwytywanie wyjątków które uniemożliwiają dalsze działanie aplikacji
# [ENG] catching exceptions that prevent further operation of the application
catch(ConfigurationException $e)
{
    echo "<h1>Wystąpił błąd w aplikacji</h1>";
    echo '<h3> Problem z konfiguracją proszę skontaktować się z administracją</h3>';
    dump($e);
    $logger->writeLogEntry($e->getMessage());
}
catch(EmailException $e)
{
    echo "<h1>Wystąpił błąd w wysyłaniu maila</h1>";
    dump($e);
    $logger->writeLogEntry($e->getMessage());
}
catch(AppException $e)
{
    echo "<h1>Wystąpił błąd w aplikacji</h1>";
    echo '<h3> Szczegóły '.$e->getMessage().'</h3>';
    dump($e);
    $logger->writeLogEntry($e->getMessage());
    //echo '<h3> Szczegóły '.$e->getPrevious()->getMessage().'</h3>';
}
catch(Throwable $e)
{
    dump($e);
    echo "<h1>Wystąpił błąd w aplikacji</h1>";
    $logger->writeLogEntry($e->getMessage());
}