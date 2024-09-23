<?php
declare(strict_types=1);

namespace APP\Exception;

use APP\Exception\AppException;

//require_once('src/Exception/AppException.php');

# [PL] klasa rozszerzajaca Exception dla wyjatków zwiazanych z działaniem MailHandlera
# [ENG] class extending Exception for exceptions related to the  MailHandler
class EmailException extends AppException
{
    

}