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




class NoteModel extends AbstractModel implements ModelNoteInterface
{
  
    
    private int $user_id;

    public function set_user(int $id)
    {
        $this->user_id=$id;

    }
   
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
    public function search(array $phrase,int $pageNumber,int $pageSize , string $sortBy, string $sortOrder) : array
    {
        return $this->findBy($phrase,$pageNumber,$pageSize,$sortBy,$sortOrder);
    }
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


    public function list(int $pageNumber,int $pageSize , string $sortBy, string $sortOrder):array
    {
        return $this->findBy(null,$pageNumber,$pageSize,$sortBy,$sortOrder);
    }

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

    public function get(int $id):array
    {
        try{
            $query="SELECT * FROM `notes` WHERE `note_id`=$id AND `user_id`=$this->user_id ";
            $result=$this->conn->query($query);
            $note=$result->fetch(PDO::FETCH_ASSOC);
            
            

        }
        catch(Throwable $e)
        {
            throw new StorageException('Nie udało sie pobrać notatki',400,$e);
        }
       if(!$note)
        {
            throw new NotFoundException("Notatka o id $id nie istneje");
         
        }
        return $note;
            

    }
    public function edit(int $id,array $data):void
    {
        try{
            $title=$this->conn->quote($data['title']);
            $description=$this->conn->quote($data['description']);
            $query="UPDATE `notes` SET `title`=$title,`description`=$description WHERE `note_id`=$id AND `user_id`=$this->user_id ";
            $this->conn->exec($query);


        }catch(Throwable $e)
        {
            throw new StorageException('Nie udało się zaktualizować notatki',400,$e);

        }


    }
    public function delete(int $id):void
    {
        try{
          
            $query="DELETE FROM `notes` WHERE `note_id`=$id AND `user_id`=$this->user_id LIMIT 1";
            $this->conn->exec($query);

        }
        catch(Throwable $e)
        {
            throw new StorageException('Nie udało się usunąć notatki',400,$e);
        }

    }
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