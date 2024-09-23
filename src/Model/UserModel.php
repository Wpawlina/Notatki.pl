<?php

declare(strict_types=1);

namespace APP\Model;

use APP\Exception\AppException;
use APP\Exception\ConfigurationException;
use APP\Exception\StorageException;
use APP\Exception\NotFoundException;
use DateTime;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;

# [PL] klasa UserModel jest odpowiedzalna za dostep do bazy danych i obsługe funkcjonalnosci zwiazanych z informacjami o uzytkowniku
# [ENG] The UserModel class is responsible for accessing the database and handling functionalities related to user information
class UserModel extends AbstractModel
{
    # [PL] metoda search zwraca hasło,id,flage aktywacji użytkownika o podanym loginie
    # [ENG] The search method returns the password, id, and activation flag of the user with the given login
    public function search(string $login): array
    {
        try
        {
            $login = $this->conn->quote($login, PDO::PARAM_STR);
            $query = "SELECT `password`,`activated`,`user_id` FROM `users` WHERE `email`=$login LIMIT 1;";
           
            $result = $this->conn->query($query);
            if ($result->rowCount() === 0)
            {
                return [];
            }
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Connection Error');
        }
        return [];
    }

    # [PL] metoda create zapisuje w bazie danych nowego uzytkownika w opraciu o przesłane dane
    # [ENG] The create method saves a new user in the database based on the provided data
    public function create(string $login, string $firstName, string $lastName, string $password, string $code): void
    {
        try
        {
            $login = $this->conn->quote($login, PDO::PARAM_STR);
            $firstName = $this->conn->quote($firstName, PDO::PARAM_STR);
            $lastName = $this->conn->quote($lastName, PDO::PARAM_STR);
            $password = $this->conn->quote($password, PDO::PARAM_STR);
            $time = new DateTime();
            $time->modify('+1 day');
            $time = $time->format('Y-m-d H:i:s');
            $time = $this->conn->quote($time, PDO::PARAM_STR);
            $code = $this->conn->quote($code, PDO::PARAM_STR);
            $query = "INSERT INTO `users`(`email`,`first_name`,`last_name`,`password`) VALUES($login, $firstName, $lastName, $password);";
            $this->conn->exec($query);
            $query = "SELECT `user_id` FROM `users` WHERE `email`=$login";
            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            $userId = $result['user_id'];
            $query = "INSERT INTO `user_codes`(`user_id`,`code`,`expiry`) VALUES($userId, $code, $time);";
            $this->conn->exec($query);
        }
        catch(Throwable $e)
        {
            throw new StorageException('Nie udało się zarejestrować użytkownika', 400, $e);
        }
    }

    # [PL] metoda show zweraca inforamacje o uzytkowniku o podanym id 
    # [ENG] The show method returns information about the user with the given id
    public function show(int $userId): array
    {
        try
        {
            $query = "SELECT `email`,`first_name`,`last_name` FROM `users` WHERE `user_id`=$userId;";
            $result = $this->conn->query($query);
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika', 400, $e);
        }
    }

    # [PL] metoda edit wstawia zmodyfikowane dane o uzytkowniku do bazy danych
    # [ENG] The edit method inserts modified user data into the database
    public function edit(int $userId, string $firstName, string $lastName): void
    {
        try
        {
            $firstName = $this->conn->quote($firstName, PDO::PARAM_STR);
            $lastName = $this->conn->quote($lastName, PDO::PARAM_STR);
            $query = "UPDATE `users` SET `first_name`=$firstName, `last_name`=$lastName WHERE `user_id`=$userId";
            $this->conn->exec($query);
        }
        catch(PDOException $e)
        {
            throw new StorageException("Nie udało się znaleść użytkownika $userId", 400, $e);
        }
    }

    # [PL] metoda delete usuwa dane o uzytkowniku o podanym id z bazy danych
    # [ENG] The delete method deletes user data with the given id from the database
    public function delete(int $userId): void
    {
        try
        {
            $query = "DELETE FROM `users` WHERE `user_id`=$userId LIMIT 1";
            $this->conn->exec($query);
        }
        catch(PDOException $e)
        {
            throw new StorageException("Nie udało się znaleść użytkownika $userId", 400, $e);
        }
    }

    # [PL] metoda list zwraca liczbe uzytkowników o podanym loginie 
    # [ENG] The list method returns the number of users with the given login
    public function list(string $login): int
    {
        try
        {
            $login = $this->conn->quote($login, PDO::PARAM_STR);
            $query = "SELECT COUNT(*) AS numOfUsers FROM `users` WHERE `email`=$login";
            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            return $result['numOfUsers'];
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika', 400, $e);
        }
    }

    # [PL] metoda getCodeInfo zwraca id, kod, date wygasniecia na podstawie przesłanego kodu aktywacji/zmiany hasła
    # [ENG] The getCodeInfo method returns the id, code, and expiry date based on the provided activation/password change code
    public function getCodeInfo(string $code): array
    {
        try
        {
            $code = $this->conn->quote($code);
            $query = "SELECT `user_id`,`code`,`expiry` FROM `user_codes` WHERE `code`=$code;";
            $result = $this->conn->query($query);
            
            if ($result->rowCount() === 0)
            {
                return [];
            }
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika', 400, $e);
        }
    }

    # [PL] metoda activate ustawia flaga aktywacji konta uzytkownika o podanym id na true
    # [ENG] The activate method sets the activation flag of the user account with the given id to true
    public function activate(int $userId): void
    {
        try
        {
            $query = "UPDATE `users` SET `activated`=1 WHERE `user_id`=$userId;";
            $this->conn->exec($query);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się aktywować użytkownika', 400, $e);
        }
    }

    # [PL] metoda wstawia kod zmiany hasła do bazy danych dla uzytkownika o podanym loginie
    # [ENG] The insertChgPasswdCode method inserts a password change code into the database for the user with the given login
    public function insertChgPasswdCode(string $login, string $code): void
    {
        try
        {
            $time = new DateTime();
            $time->modify('+1 hour');
            $time = $time->format('Y-m-d H:i:s');
            $userId = $this->getUserIdfromEmail($login);
            $time = $this->conn->quote($time, PDO::PARAM_STR);
            $code = $this->conn->quote($code, PDO::PARAM_STR);
            $query = "INSERT INTO `user_codes`(`user_id`,`code`,`expiry`) VALUES($userId, $code, $time);";
            $this->conn->exec($query);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się dodać kodu zmiany', 400, $e);
        }
    }

    # [PL] metoda updatePassword zmienia hasło zapisane w bazie danych dla uzytkownika o podanym id
    # [ENG] The updatePassword method changes the password stored in the database for the user with the given id
    public function updatePassword(int $userId, string $password): void
    {
        try
        {
            $password = $this->conn->quote($password, PDO::PARAM_STR);
            $query = "UPDATE `users` SET `password`=$password WHERE `user_id`=$userId;";
            $this->conn->exec($query);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się zmienić hasła', 400, $e);
        }
    }

    # [PL] metoda zwraca email uzytkownika o podanym id
    # [ENG] The getEmailFromUserId method returns the email of the user with the given id
    public function getEmailFromUserId(int $userId): string
    {
        try
        {
            $query = "SELECT `email` FROM `users` WHERE `user_id`=$userId;";
            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            return $result['email'];
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika', 400, $e);
        }
    }

    # [PL] metoda usuwa z bazy danych kod aktywacji/zmiany hasła 
    # [ENG] The deleteUsedCode method deletes the activation/password change code from the database
    public function deleteUsedCode(string $code): void
    {
        try
        {
            $code = $this->conn->quote($code);
            $query = "DELETE FROM `user_codes` WHERE `code`=$code LIMIT 1;";
            $this->conn->exec($query);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się usunąć kodu', 400, $e);
        }
    }

    # [PL] metoda zwraca id uzytkownika o podanym loginie
    # [ENG] The getUserIdfromEmail method returns the user id with the given login
    private function getUserIdfromEmail(string $login): int
    {
        try
        {
            $login = $this->conn->quote($login);
            $query = "SELECT `user_id` FROM `users` WHERE `email`=$login";
            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            return $result['user_id'];
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika', 400, $e);
        }
    }
}