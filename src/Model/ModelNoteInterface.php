<?php

declare(strict_types=1);

namespace APP\Model;


# interface opisuje podstawowe operacje jakie musi implementowac klasa obsługujaca notatki w bazie danych
interface ModelNoteInterface
{
    #Metoda list zwraca wszytkie notatki w podanej kolejnosci
    public function list(int $pageNumber,int $pageSize , string $sortBy, string $sortOrder):array;

    #metoda search znajduje notatki które spełniaja podane warunki i zwraca je w podanej kolejnosci
    public function search(array $phrase,int $pageNumber,int $pageSize , string $sortBy, string $sortOrder) : array;

    #metoda count zwraca ile notatek posiada zalogowany uzytkownik
    public function count():int;

    #metoda searchCount zwraca ile spełniajacych warunki notatek posiada zalogowany uzytkownik
    public function searchCount(array $phrase) : int;

    #metoda get zwraca dane o notatce o podanym id 
    public function get(int $id):array;

    #metoda create tworzy notatke o podanych parametrach i wstawia ja do bazy danych
    public function create(array $data):void;

    #metoda edytuje istniejaca notatke o podanym id aktulizujac ja w bazie danych w oparciu o podane dane 
    public function edit(int $id,array $data):void;

    #metoda delete usuwa notatke o podanym id
    public function delete(int $id):void;

}