<?php

require_once '../helpers/imports.php';


$signup = new Signup(DataBase::getInstance());
$signup->registerUser();


class Signup {
    private PDO $db;
    private string $email, $password, $username;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->email = HelperMethods::secureRequest('email');
        $this->password = HelperMethods::secureRequest('password');
        $this->username = HelperMethods::secureRequest('username');
    }

    public function registerUser(): void {
        try {
            $this->validateCredentials();

            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

            $this->insertNewUser($hashedPassword);

            HelperMethods::sendResponse(null, Constants::REGISTRATION_SUCCESS, 200);
        } catch (PDOException $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }


    private function validateCredentials(): void {
        $this->assertCondition(!$this->areFieldsEmpty(), Constants::ALL_FIELDS_REQUIRED);
        $this->assertCondition($this->isValidEmail(), Constants::INVALID_EMAIL);
        $this->assertCondition(!$this->userExists(), Constants::USER_ALREADY_EXISTS);
    }

    private function assertCondition($condition, $message): void {
        if (!$condition) {
            HelperMethods::sendResponse(null, $message, 400);
        }
    }

    private function areFieldsEmpty(): bool {
        return empty($this->email) || empty($this->password) || empty($this->username);
    }

    private function isValidEmail(): bool {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    private function userExists(): bool {
        $query = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $query->execute([$this->email]);
        return $query->fetch() !== false;
    }

    public function insertNewUser(string $hashedPassword): void {
        $query = $this->db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');

        $query->execute([$this->username, $this->email, $hashedPassword]);
    }
}
