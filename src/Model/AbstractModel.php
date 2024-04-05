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


abstract class AbstractModel
{
    protected PDO $conn;
    
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

    private function createConnection(array $config)
    {
              
        $dsn="mysql:dbname={$config['database']};host={$config['host']}";
        $this->conn=new PDO($dsn,
                        $config['user'],
                        $config['password'],
                        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]

        ); 
    }
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