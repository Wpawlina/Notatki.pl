<?php 

declare(strict_types=1);

namespace APP\Model;

use APP\Exception\AppException;
use APP\Exception\ConfigurationException;
use APP\Exception\StorageException;
use APP\Exception\NotFoundException;

use PDO;
use PDOException;
use Throwable;

#klasa abstractModel zawiera podstawowe metody potrzebne do działania kontrolera bazy danych nastepnie jest rozszerzana o wymagane metody przez NoteModel i UserModel
abstract class AbstractModel
{
    protected PDO $conn;
    
    #konstruktor testuje czy konfiguracja jest poprawna i tworzy połaczenie z bazą danych
    public function __construct(array $config)
    {
       try{
        $this->validateConfig($config);
        $this->createConnection($config);
        }
        catch(PDOException $e)
        {
           
            throw new StorageException('Connection Error');
          
        }
    }
    #metoda odpowiedzalna za utworzenie i zapisanie w własiwosiciach połaczenia do bazy danych
    private function createConnection(array $config): void
    {
              
        $dsn="mysql:dbname={$config['database']};host={$config['host']}";
        $this->conn=new PDO($dsn,
                        $config['user'],
                        $config['password'],
                        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]

        ); 
    }

    #metoda odpowiedzalna za sprawdzenie poprawnosci konfiguracji
    private function validateConfig(array $config ) :void
    {
        if(
            empty($config['database'])
            ||empty($config['host'])
            || empty($config['user'])
            || empty($config['password']) )
        {
            throw new ConfigurationException('Storage configuration error');
        }
    }



}