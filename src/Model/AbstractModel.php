<?php

declare(strict_types=1);

namespace APP\Model;

use PDO;
use PDOException;
use APP\Exception\ConfigurationException;
use APP\Exception\StorageException;

# [PL] Klasa AbstractModel jest odpowiedzialna za zarządzanie połączeniem z bazą danych
# [ENG] The AbstractModel class is responsible for managing the database connection
abstract class AbstractModel
{
    protected PDO $conn;

    # [PL] Konstruktor tworzy połączenie z bazą danych na podstawie przekazanej konfiguracji
    # [ENG] The constructor creates a database connection based on the provided configuration
    public function __construct(array $config)
    {
        $this->validateConfig($config);
        $this->createConnection($config);
    }

    # [PL] Metoda odpowiedzialna za utworzenie i zapisanie w właściwościach połączenia do bazy danych
    # [ENG] Method responsible for creating and saving the database connection in the properties
    private function createConnection(array $config): void
    {
        $dsn = "mysql:dbname={$config['database']};host={$config['host']}";
        $this->conn = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    # [PL] Metoda odpowiedzialna za sprawdzenie poprawności konfiguracji
    # [ENG] Method responsible for validating the configuration
    private function validateConfig(array $config): void
    {
        if (
            empty($config['database'])
            || empty($config['host'])
            || empty($config['user'])
            || empty($config['password'])
        ) {
            throw new ConfigurationException('Storage configuration error');
        }
    }
}