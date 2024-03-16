<?php

require_once '../helpers/imports.php';

$notes = new GetAllNotes(Database::getInstance());
$notes->getAllNotes();

class GetAllNotes {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllNotes(): void {
        try {
            $query = $this->db->prepare('SELECT * FROM notes');
            $query->execute();
            $notes = $query->fetchAll();

            HelperMethods::sendResponse($notes, Constants::NOTES_FETCHED, 200);
        } catch (PDOException $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }
}
