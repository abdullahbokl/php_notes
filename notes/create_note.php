<?php

$note = new CreateNote(Database::getInstance());
$note->createNote();

class CreateNote {
    private PDO $db;
    public string $title, $content, $users;
    public array $images;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->title = HelperMethods::secureRequest('title');
        $this->content = HelperMethods::secureRequest('content');
        $this->users = HelperMethods::secureRequest('users');
        $this->images = $_FILES['images'];
    }

    public function createNote(): void {
        try {
            $imagePath = HelperMethods::uploadFile($this->images, 'notes_images/');


            $query = $this->db->prepare('INSERT INTO notes (title, content, images, users) VALUES (?, ?, ?, ?)');
            $query->execute([$this->title, $this->content, $imagePath, $this->users]);
            $query->fetch();

            $lastId = $this->db->lastInsertId();

            HelperMethods::sendResponse(['id' => $lastId], Constants::NOTE_CREATED, 200);
        } catch (PDOException $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }
}