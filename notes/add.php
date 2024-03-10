<?php

require_once '../helpers/imports.php';

$note = new AddNote();
$note->addNote();

class AddNote {
    private PDO $db;
    public string $title, $content, $images, $users;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->title = HelperMethods::secureRequest('title');
        $this->content = HelperMethods::secureRequest('content');
        $this->images = HelperMethods::secureRequest('images');
        $this->users = HelperMethods::secureRequest('users');
    }

    public function addNote(): void {
        try {

            $query = $this->db->prepare('INSERT INTO notes (title, content, images, users) VALUES (?, ?, ?, ?)');
            $query->execute([$this->title, $this->content, $this->images, $this->users]);
            $query->fetch();

            $lastId = $this->db->lastInsertId();

            HelperMethods::sendResponse(['id' => $lastId], Constants::NOTE_CREATED, 200);
        } catch (PDOException $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }
}