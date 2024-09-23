<?php

declare(strict_types=1);

namespace APP\Logger;

use APP\Exception\FileException;
use DateTime;
use Throwable;

# [PL] klasa logger jest odpowiedzalana za zapisywanie informacji o błedach w postaci loggów do pliku log.txt
# [ENG] The Logger class is responsible for saving error information in the form of logs to the log.txt file
class Logger
{
    private $file;
    
    # [PL] konstruktor tworzy obiekt loggera i przypisuje do niego konifiguracje zawarta w config.php
    # [ENG] The constructor creates a Logger object and assigns the configuration contained in config.php to it
    public function __construct(array $config)
    {
        try
        {
            $this->file = fopen($config['fileName'], 'a');
            if (!$this->file)
            {
                throw new FileException("File $this->file not found");
            }
        }
        catch (Throwable $e)
        {
            throw new FileException("File $this->file not found");
        }  
    }

    # [PL] destruktor zapewnia ze po zamkniecu aplikacji dostep do pliku jest zamykany
    # [ENG] The destructor ensures that access to the file is closed after the application is closed
    public function __destruct()
    {
        fclose($this->file);
    }

    # [PL] funkcja jest opdowiedzalan za zapisanie logu do pliku napodstawie wiadomosci jaka otrzyma
    # [ENG] The function is responsible for saving the log to the file based on the message it receives
    public function writeLogEntry(string $message): void 
    {
        $curTime = new DateTime();
        $curTime = $curTime->format('Y-m-d H:i:s');
        fwrite($this->file, "\r\n================================\r\n");
        fwrite($this->file, "date: $curTime \r\n");   
        fwrite($this->file, "message: $message \r\n");
        fwrite($this->file, "\r\n================================\r\n");
    }
}