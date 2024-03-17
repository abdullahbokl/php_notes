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
            $note = $this->getCurrentNote();

            HelperMethods::deleteFile("notes_images/" . $note['images'] ?? null);

            $query = $this->db->prepare('DELETE FROM notes WHERE id = ?');
            $query->execute([$this->id]);
            $query->fetch();

            HelperMethods::sendResponse(null, Constants::NOTE_DELETED, 200);
        } catch (PDOException $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }

    /**
     * @return array
     */
    public function getCurrentNote(): array {
        $query = $this->db->prepare('SELECT images FROM notes WHERE id = ?');
        $query->execute([$this->id]);
        return $query->fetch();
    }
}