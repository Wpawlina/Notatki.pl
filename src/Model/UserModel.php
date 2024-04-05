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


class UserModel extends AbstractModel
{
    public function search(string $login):array
    {
        try
        {
            $login=$this->conn->quote($login,PDO::PARAM_STR);
            $query="SELECT `password`,`activated`,`user_id` FROM `users` WHERE `email`=$login LIMIT 1;";
           
            $result=$this->conn->query($query);
            if($result->rowCount()===0)
            {
                return [];
            }
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
           
           throw new StorageException(' Connection Error');

        }
         return [];
        
    }
    public function create(string $login,string $firstName, string $lastName , string $password,string $code):void
    {
        try
        {
            $login=$this->conn->quote($login,PDO::PARAM_STR);
            $firstName=$this->conn->quote($firstName,PDO::PARAM_STR);
            $lastName=$this->conn->quote($lastName,PDO::PARAM_STR);
            $password=$this->conn->quote($password,PDO::PARAM_STR);
            $time=new DateTime();
            $time->modify('+1 day');
            $time=$time->format('Y-m-d H:i:s');
            $time=$this->conn->quote($time,PDO::PARAM_STR);
            $code=$this->conn->quote($code,PDO::PARAM_STR);
            $query="INSERT INTO `users`(`email`,`first_name`,`last_name`,`password`) VALUES($login,$firstName,$lastName,$password);";
            $this->conn->exec($query);
            $query="SELECT `user_id` FROM `users` WHERE `email`=$login";
            $result=$this->conn->query($query);
            $result=$result->fetch(PDO::FETCH_ASSOC);
            $userId=$result['user_id'];
            $query="INSERT INTO `user_codes`(`user_id`,`code`,`expiry`) VALUES($userId,$code,$time);";
            $this->conn->exec($query);
        }
        catch(Throwable $e)
        {
           
            throw new StorageException('Nie udało się zarejestrować użytkownika',400,$e);

        }
       

    }
    public function show(int $userId):array
    {
        try
        {
        $query="SELECT `email`,`first_name`,`last_name` FROM `users` WHERE `user_id`=$userId;";
        $result=$this->conn->query($query);
        return $result->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika',400,$e);
        }
    }
    public function edit(int $userId,string $firstName,string $lastName):void
    {
        try
        {
            $firstName=$this->conn->quote($firstName,PDO::PARAM_STR);
            $lastName=$this->conn->quote($lastName,PDO::PARAM_STR);
            $query="UPDATE `users` SET `first_name`=$firstName,`last_name`=$lastName WHERE `user_id`=$userId";
            $this->conn->exec($query);
        }catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika',400,$e);
        }
    }
    public function delete(int $userId): void
    {
        try
        {
            $query="DELETE FROM `users` WHERE `user_id`=$userId LIMIT 1";
            $this->conn->exec($query);
        }catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika',400,$e);
        }
      

    }
    public function list(string $login):int
    {
        try
        {
            $login=$this->conn->quote($login,PDO::PARAM_STR);
            $query="SELECT COUNT(*) AS numOfUsers FROM `users` WHERE `email`=$login";
            $result=$this->conn->query($query);
            $result=$result->fetch(PDO::FETCH_ASSOC);
            return $result['numOfUsers'];


        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika',400,$e);
        }


    }
    public function getCodeInfo(string $code): array
    {
        try
        {
            $code=$this->conn->quote($code);
            $query="SELECT `user_id`,`code`,`expiry` FROM `user_codes` WHERE `code`=$code;";
            $result=$this->conn->query($query);
            
            if($result->rowCount()===0)
            {
                return [];
            }
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika',400,$e);
        }
    }
    public function activate(int $userId):void
    {
        try
        {
            $query="UPDATE `users` SET `activated`=1 WHERE `user_id`=$userId;";
            $this->conn->exec($query);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się aktywować użytkownika',400,$e);
        }
    }
    public function insertChgPasswdCode(string $login,string $code):void
    {
        try
        {
            $time=new DateTime();
            $time->modify('+1 hour');
            $time=$time->format('Y-m-d H:i:s');
            $userId=$this->getUserIdfromEmail($login);
            $time=$this->conn->quote($time,PDO::PARAM_STR);
            $code=$this->conn->quote($code,PDO::PARAM_STR);
            $query="INSERT INTO `user_codes`(`user_id`,`code`,`expiry`) VALUES($userId,$code,$time);";
            $this->conn->exec($query);


        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się dodać kodu zmiany',400,$e);
        }


    }
    public function updatePassword(int $userId,string $password):void
    {
        try
        {
            $password=$this->conn->quote($password,PDO::PARAM_STR);
            $query="UPDATE `users` SET `password`=$password WHERE `user_id`=$userId;";
            $this->conn->exec($query);
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się zmienić hasła',400,$e);
        }
        
        
    }
    public function getEmailFromUserId(int $userId):string
    {
        try
        {
            $query="SELECT `email` FROM `users` WHERE `user_id`=$userId;";
            $result=$this->conn->query($query);
            $result=$result->fetch(PDO::FETCH_ASSOC);
            return $result['email'];
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika',400,$e);
        } 


    }
    public function deleteUsedCode(string $code):void
    {
        try
        {
            $code=$this->conn->quote($code);
            $query="DELETE FROM `user_codes` WHERE `code`=$code LIMIT 1;";
            $this->conn->exec($query);

        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się usunąć kodu',400,$e);
        } 
        
        
    }

    private function getUserIdfromEmail(string $login):int
    {
        try
        {
            $login=$this->conn->quote($login);
            $query="SELECT `user_id` FROM `users` WHERE `email`=$login";
            $result=$this->conn->query($query);
            $result=$result->fetch(PDO::FETCH_ASSOC);
            return $result['user_id'];
        }
        catch(PDOException $e)
        {
            throw new StorageException('Nie udało się znaleść użytkownika',400,$e);
        }
       
    }
       

    
}