<?php

require_once '../helpers/imports.php';

$note = new UpdateNote(Database::getInstance());
$note->updateNote();

class UpdateNote {
    private PDO $db;
    public string $id, $title, $content, $images, $users;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->id = HelperMethods::secureRequest('id');
        $this->title = HelperMethods::secureRequest('title');
        $this->content = HelperMethods::secureRequest('content');
        $this->images = HelperMethods::secureRequest('images');
        $this->users = HelperMethods::secureRequest('users');
    }

    public function updateNote(): void {
        try {
            $query = $this->db->prepare('UPDATE notes SET title = ?, content = ?, images = ?, users = ? WHERE id = ?');
            $query->execute([$this->title, $this->content, $this->images, $this->users, $this->id]);
            $query->fetch();

            HelperMethods::sendResponse(null, Constants::NOTE_UPDATED, 200);
        } catch (PDOException $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }
}