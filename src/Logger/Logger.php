<?php

declare(strict_types=1);


namespace APP\Logger;

use APP\Exception\FileException;
use DateTime;



use Throwable;


# klasa logger jest odpowiedzalana za zapisywanie informacji o błedach w postaci loggów do pliku log.txt
class Logger
{
    private $file;
    
    #konstruktor tworzy obiekt loggera i przypisuje do niego konifiguracje zawarta w config.php
    public function __construct(array $config)
    {
        try
        {
            $this->file=fopen($config['fileName'],'a');
            if(!$this->file)
            {
                throw new FileException("File $this->file not found");
            }

        }
        catch(Throwable $e)
        {
            throw new FileException("File $this->file not found");
        }  
    }
    # destruktor zapewnia ze po zamkniecu aplikacji dostep do pliku jest zamykany
    public function __destruct()
    {
        fclose($this->file);
    }
    # funkcja jest opdowiedzalan za zapisanie logu do pliku napodstawie wiadomosci jaka otrzyma
    public function writeLogEntry(string $message): void 
    {
        $curTime=new DateTime();
        $curTime=$curTime->format('Y-m-d H:i:s');
        fwrite($this->file,"\r\n================================\r\n");
        fwrite($this->file,"date: $curTime \r\n");   
        fwrite($this->file,"message: $message \r\n");
        fwrite($this->file,"\r\n================================\r\n");
    }



}