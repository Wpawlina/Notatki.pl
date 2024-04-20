<?php
declare(strict_types=1);

namespace APP\Exception;

use APP\Exception\AppException;

//require_once('src/Exception/AppException.php');

# klasa rozszerzajaca Exception dla wyjatków zwiazanych z nie znalezieniem informacj o podanej notatce lub uztkowniku w bazie danych
class NotFoundException extends AppException
{

}