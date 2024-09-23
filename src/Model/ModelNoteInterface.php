<?php

declare(strict_types=1);

namespace APP\Model;

# [PL] interface opisuje podstawowe operacje jakie musi implementowac klasa obsługujaca notatki w bazie danych
# [ENG] The interface describes the basic operations that a class handling notes in the database must implement
interface ModelNoteInterface
{
    # [PL] Metoda list zwraca wszytkie notatki w podanej kolejnosci
    # [ENG] The list method returns all notes in the given order
    public function list(int $pageNumber, int $pageSize, string $sortBy, string $sortOrder): array;

    # [PL] metoda search znajduje notatki które spełniaja podane warunki i zwraca je w podanej kolejnosci
    # [ENG] The search method finds notes that meet the given conditions and returns them in the given order
    public function search(array $phrase, int $pageNumber, int $pageSize, string $sortBy, string $sortOrder): array;

    # [PL] metoda count zwraca ile notatek posiada zalogowany uzytkownik
    # [ENG] The count method returns how many notes the logged-in user has
    public function count(): int;

    # [PL] metoda searchCount zwraca ile spełniajacych warunki notatek posiada zalogowany uzytkownik
    # [ENG] The searchCount method returns how many notes meeting the conditions the logged-in user has
    public function searchCount(array $phrase): int;

    # [PL] metoda get zwraca dane o notatce o podanym id 
    # [ENG] The get method returns data about the note with the given id
    public function get(int $id): array;

    # [PL] metoda create tworzy notatke o podanych parametrach i wstawia ja do bazy danych
    # [ENG] The create method creates a note with the given parameters and inserts it into the database
    public function create(array $data): void;

    # [PL] metoda edytuje istniejaca notatke o podanym id aktulizujac ja w bazie danych w oparciu o podane dane 
    # [ENG] The edit method edits an existing note with the given id, updating it in the database based on the given data
    public function edit(int $id, array $data): void;

    # [PL] metoda delete usuwa notatke o podanym id
    # [ENG] The delete method deletes the note with the given id
    public function delete(int $id): void;
}