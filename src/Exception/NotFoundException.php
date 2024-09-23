<?php
declare(strict_types=1);

namespace APP\Exception;

use APP\Exception\AppException;

//require_once('src/Exception/AppException.php');

# [PL] klasa rozszerzajaca Exception dla wyjatków zwiazanych z nie znalezieniem informacj o podanej notatce lub uztkowniku w bazie danych
# [ENG] class extending Exception for exceptions related to not finding information about the given note or user in the database
class NotFoundException extends AppException
{

}