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

# [PL] Klasa AbstractController zawiera podstawowe funkcjonalności kontrolera w modelu MVC takie jak przypisanie konfiguracji, uruchomienie pozostałych elementów aplikacji oraz przekierowania.
# Jest ona rozszerzana przez klasę MainController, która odpowiada za implementację poszczególnych zadań kontrolera w modelu MVC. 

#  [ENG] The AbstractController class contains basic functionalities of a controller in the MVC model such as configuration assignment, starting other application elements, and redirections.
# It is extended by the MainController class, which is responsible for implementing specific tasks of the controller in the MVC model.
abstract class AbstractController
{
    protected string $defaultAction = 'searchUser'; 

    protected static array $config = [];

    protected Logger $logger;
    protected Request $request;
    protected View $view;
    protected NoteModel $noteModel;
    protected UserModel $userModel;
    protected MailHandler $mailHandler;

    # [PL] Funkcja służy przypisaniu do klasy konfiguracji kontrolera z pliku config.php. 
    # [ENG] The function assigns the controller's configuration from the config.php file to the class. 
    public static function initConfiguration(array $config): void
    {
        self::$config = $config; 
    }

    # [PL] Konstruktor klasy inicjalizuje lub przypisuje obiekty klas używanych w aplikacji, takich jak view (odpowiedzialne za wyświetlanie HTML), 
    # modele (odpowiedzialne za dostęp do bazy danych), request (odpowiedzialny za dostęp do HTTP), mail handler (wysyłanie e-maili) oraz logger (zapisywanie logów błędów). 

    # [ENG] The class constructor initializes or assigns the class objects used in the application, such as view (responsible for rendering HTML), 
    # models (for database access), request (HTTP access), mail handler (email sending), and logger (error logging). 
    public function __construct(Request $request, Logger $logger)
    {
        if (empty(self::$config['db'])) {
            throw new ConfigurationException('Configuration error');
        }

        $this->noteModel = new NoteModel(self::$config['db']);
        $this->userModel = new UserModel(self::$config['db']);
        $this->mailHandler = new MailHandler(self::$config['mail']);
        $this->request = $request;
        $this->view = new View();
        $this->logger = $logger;
    }

    # [PL] Funkcja run uruchamia odpowiednią akcję kontrolera na podstawie informacji z protokołu HTTP. Zawiera zabezpieczenia takie jak ograniczenie dostępu dla niezalogowanych użytkowników i sesji hijacking. 

    # [ENG] The run function triggers the appropriate controller action based on the HTTP protocol information. It includes security measures such as limiting access for non-logged-in users and session hijacking prevention. 
    public function run(): void
    {    
        try {
            # [PL] Zabezpieczenie przed Session Hijacking poprzez weryfikację HTTP_USER_AGENT.
            #  [ENG]  Protection against Session Hijacking by verifying HTTP_USER_AGENT.
            if (!empty($this->request->sessionParam('HTTP_USER_AGENT', null))) {
                if ($this->request->serverParam('HTTP_USER_AGENT', null) !== $this->request->sessionParam('HTTP_USER_AGENT', null)) {
                    session_destroy();
                    $this->redirect('index.php', []);
                }
            } else {
                $this->request->setUserAgentSession(($this->request->serverParam('HTTP_USER_AGENT')));
            }

            # [PL] W zależności od zalogowania użytkownika ustawienie domyślnej akcji kontrolera. 
            # [ENG] Set the default controller action depending on whether the user is logged in. 
            if ($this->request->sessionParam('user_id', null)) {
                $this->defaultAction = 'listNotes';
                $this->noteModel->set_user($this->request->sessionParam('user_id', null));
            }

            # [PL] Wywołanie odpowiedniej akcji na podstawie parametrów z protokołu HTTP. 
            # [ENG] Calling the appropriate action based on HTTP protocol parameters. 
            $action = $this->action() . 'Action';
            if (!method_exists($this, $action)) {
                $action = $this->defaultAction . 'Action';   
            }

            #  [PL] Ograniczenie dostępu do funkcji przeznaczonych wyłącznie dla zalogowanych użytkowników.
            #   [ENG] Restrict access to functions dedicated only to logged-in users.
            if (!$this->request->sessionParam('user_id', null)) {
                if (!in_array($action, ['searchUserAction', 'createUserAction', 'termsAction', 'activateUserAction', 'chgPasswdAction'])) {
                    $this->redirect('index.php', ['error' => 'missingUser']);
                }
            }
            $this->$action();

        # [PL] Obsługa wyjątków podczas działania funkcji run. 
        # [ENG]  Handling exceptions during the run function execution.
        } catch (StorageException $e) {
            $this->logger->writeLogEntry($e->getMessage());
            $this->view->render('error', ['message' => $e->getMessage()]);
        } catch (NotFoundException $e) {
            $this->logger->writeLogEntry($e->getMessage());
            $this->redirect('index.php', ['error' => 'NotFound']);
        } catch (WrongCredentialsException $e) {
            $this->logger->writeLogEntry($e->getMessage());
            $this->redirect('index.php', ['error' => 'WrongCredentials']);
        } catch (NotActivatedException $e) {
            $this->logger->writeLogEntry($e->getMessage());
            $this->redirect('index.php', ['error' => 'NotActivated']);
        }
    }

    # [PL] Funkcja jest odpowiedzialna za przekierowania przy użyciu funkcji header. 
    # [ENG] The function is responsible for redirections using the header function. 
    final protected function redirect(string $to, array $params): void
    {
        $location = $to;
        $queryParams = [];
        if (count($params)) {
            foreach ($params as $key => $value) {
                $queryParams[] = urlencode($key) . '=' . urlencode($value);
            }
            $queryParams = implode('&', $queryParams);
            $location .= '?' . $queryParams;
        }

        header("Location: $location");
        exit();
    }
    
    # [PL] Funkcja action zwraca odpowiednią nazwę metody w zależności od parametrów przesłanych przez protokół HTTP. 
    #  [ENG] The action function returns the appropriate method name based on parameters sent via the HTTP protocol. 
    private function action(): string
    {
        $action = $this->request->getParam('action', $this->defaultAction);
        return $action ?? $this->defaultAction;
    }
}
