<?php
declare(strict_types=1);

namespace APP\Model;

//require_once('src/Exception/StorageException.php');
//require_once('src/Exception/NotFoundException.php');

use APP\Exception\AppException;
use APP\Exception\ConfigurationException;
use APP\Exception\StorageException;
use APP\Exception\NotFoundException;

use PDO;
use PDOException;
use Throwable;



#klasa NoteModel jest odpowiedzalna za dostep do bazy danych i obsługe funkcjonalnosci zwiazanych z notatkami użytkowników
class NoteModel extends AbstractModel implements ModelNoteInterface
{
  
    
    private int $user_id;
    
    #metoda set_user zapisuje id uzytkownika którego notatki bedą przetwarzane przez inne metody
    public function set_user(int $id)
    {
        $this->user_id=$id;

    }
    

    #metoda create tworzy notatke o podanych parametrach i wstawia ja do bazy danych
    public function create(array $data):void
    {
        try
        {
            
            $title=$this->conn->quote($data['title']);
            $description=$this->conn->quote($data['description']);
            $created=$this->conn->quote(date('Y-m-d H:i:s'));
            $query="INSERT INTO `notes`(`title`,`description`,`created`,`user_id`)
            VALUES($title,$description,$created,$this->user_id)";
           
            $this->conn->exec($query);
        }
        catch(Throwable $e)
        {
           
            throw new StorageException('Nie udało się utworzyć notatki',400,$e);

        }
        
    }
    #metoda search znajduje notatki które spełniaja podane warunki i wypsiuje je w podanej kolejnosci
    public function search(array $phrase,int $pageNumber,int $pageSize , string $sortBy, string $sortOrder) : array
    {
        return $this->findBy($phrase,$pageNumber,$pageSize,$sortBy,$sortOrder);
    }
    #metoda searchCount zwraca ile spełniajacych warunki notatek posiada zalogowany uzytkownik
    public function searchCount(array $phrase) : int
    {
        
        try
        {
            $whereClause=$this->getWhere($phrase);
            
            $query="SELECT COUNT(*) AS cn FROM `notes`  $whereClause ";
            $result=$this->conn->query($query);
            $result=$result->fetch(PDO::FETCH_ASSOC);
            if($result===false)
            {   
                throw new StorageException('Błąd przy próbie pobrania ilości notatek');
            
            }
            return (int) $result['cn'];
        
        }  catch(Throwable $e)
            {
                throw new StorageException('Nie udało się pobrać informacji o liczbie  notatek',400,$e);

            } 
        
        
    }

    #Metoda list wypisuje wszytkie notatki w podanej kolejnosci
    public function list(int $pageNumber,int $pageSize , string $sortBy, string $sortOrder):array
    {
        return $this->findBy(null,$pageNumber,$pageSize,$sortBy,$sortOrder);
    }

    #metoda count zwraca ile notatek posiada zalogowany uzytkownik
    public function count():int
    {
        try
        {
        $query="SELECT COUNT(*) AS cn FROM `notes` WHERE `user_id`=$this->user_id";
        $result=$this->conn->query($query);
        $result=$result->fetch(PDO::FETCH_ASSOC);
        if($result===false)
        {   
            throw new StorageException('Błąd przy próbie pobrania ilości notatek');
           
        }
        return (int) $result['cn'];
      
        }
        catch(Throwable $e)
        {
            throw new StorageException('Nie udało się pobrać informacji o liczbie  notatek',400,$e);
            exit();

        }
    }

    #metoda get zwraca dane o notatce o podanym id 
    public function get(int $id):array
    {
        try{
            $query="SELECT * FROM `notes` WHERE `note_id`=$id AND `user_id`=$this->user_id ";
            $result=$this->conn->query($query);
            $note=$result->fetch(PDO::FETCH_ASSOC);
            
            

        }
        catch(Throwable $e)
        {
            throw new StorageException("Nie udało sie pobrać notatki o id: $id " ,400,$e);
        }
       if(!$note)
        {
            throw new NotFoundException("Notatka o id $id nie istneje");
         
        }
        return $note;
            

    }
    #metoda edytuje istniejaca notatke o podanym id aktulizujac ja w bazie danych w oparciu o podane dane
    public function edit(int $id,array $data):void
    {
        try{
            $title=$this->conn->quote($data['title']);
            $description=$this->conn->quote($data['description']);
            $query="UPDATE `notes` SET `title`=$title,`description`=$description WHERE `note_id`=$id AND `user_id`=$this->user_id ";
            $this->conn->exec($query);


        }catch(Throwable $e)
        {
            throw new StorageException("Nie udało się zaktualizować notatki o id: $id ",400,$e);

        }


    }
    #metoda delete usuwa notatke o podanym id
    public function delete(int $id):void
    {
        try{
          
            $query="DELETE FROM `notes` WHERE `note_id`=$id AND `user_id`=$this->user_id LIMIT 1";
            $this->conn->exec($query);

        }
        catch(Throwable $e)
        {
            throw new StorageException("Nie udało się usunąć notatki o id: $id",400,$e);
        }

    }
    # metoda findBy znajduje notatki o podanych parametrach i zwraca je  w określonym formacie i kolejności
    private function findBy(?array $phrase,int $pageNumber,int $pageSize , string $sortBy, string $sortOrder):array
    {
        try
        {
            $limit=$pageSize;
            $offset=($pageNumber-1)*$pageSize;
            

            if(!in_array($sortBy,['created','title']))
            {
                $sortBy='title';
            }
            if(!in_array($sortOrder,['asc','desc']))
            {
                $sortOrder='desc';
            }

            $whereClause="WHERE `user_id`=$this->user_id";
            if($phrase)
            {
                $whereClause=$this->getWhere($phrase);
            }
           
            
        $query="SELECT `note_id`,`title`,`created` FROM `notes`  $whereClause  ORDER BY $sortBy $sortOrder LIMIT $offset,$limit ";
       
       
      
        $result=$this->conn->query($query);
        
        return $result->fetchAll(PDO::FETCH_ASSOC);
      
        }
        catch(Throwable $e)
        {
            throw new StorageException('Nie udało się wyszukac notatek',400,$e);

        }
        

    }
    #metoda getWhere tworzy klauzle WHERE jezyka SQL w oparicu o podane parametry
    private function getWhere($phrase):string
    {
      
        $title=$phrase['title']??null;
        $date=$phrase['date']??null;

        if(($date)&&($title))
        {
           $date=$this->conn->quote('%' . $date.'%',PDO::PARAM_STR );
           $title=$this->conn->quote('%'. $title.'%',PDO::PARAM_STR);
           $whereClause="  WHERE `title` LIKE $title AND `created` LIKE $date AND `user_id`=$this->user_id ";
        }
        else if($date)
        {
            $date=$this->conn->quote('%' . $date.'%',PDO::PARAM_STR );
            $whereClause=" WHERE `created` LIKE $date AND `user_id`=$this->user_id ";
        }
        else if($title)
        {
            $title=$this->conn->quote('%'. $title.'%',PDO::PARAM_STR);
            $whereClause=" WHERE `title` LIKE $title AND `user_id`=$this->user_id";
        }
        else
        {
            $whereClause="WHERE `user_id`=$this->user_id";
        }
        return $whereClause;

    }


    

   



}