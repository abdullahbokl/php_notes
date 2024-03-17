<?php


$note = new GetNoteById(Database::getInstance());
$note->getNoteById();

class GetNoteById {
    private PDO $db;
    public string $id;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->id = HelperMethods::secureRequest('id');
    }

    public function getNoteById(): void {
        try {
            $query = $this->db->prepare('SELECT * FROM notes WHERE id = ?');
            $query->execute([$this->id]);
            $note = $query->fetch();

            HelperMethods::sendResponse($note, Constants::NOTE_FETCHED, 200);
        } catch (PDOException $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }
}