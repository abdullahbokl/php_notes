<?php


$note = new DeleteNote(Database::getInstance());
$note->deleteNote();

class DeleteNote {
    private PDO $db;
    public string $id;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->id = HelperMethods::secureRequest('id');
    }

    public function deleteNote(): void {
        try {
            $query = $this->db->prepare('DELETE FROM notes WHERE id = ?');
            $query->execute([$this->id]);
            $query->fetch();

            HelperMethods::sendResponse(null, Constants::NOTE_DELETED, 200);
        } catch (PDOException $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }
}