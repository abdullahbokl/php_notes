<?php

require_once '../connect.php';
require_once '../helpers/imports.php';


$login = new Login(DataBase::getInstance());
$login->loginUser();


class Login {

    private PDO $db;
    private string $email, $password;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->email = HelperMethods::secureRequest('email');
        $this->password = HelperMethods::secureRequest('password');
    }

    public function loginUser(): void {
        $this->validateCredentials();

        $user = $this->getUser();

        $token = $this->generateToken();

        HelperMethods::sendResponse(
            [
                'username' => $user['username'],
                'email' => $user['email'],
                'token' => $token,
            ],
            Constants::LOGIN_SUCCESS,
            200
        );
    }

    private function validateCredentials(): void {
        $this->assertCondition(!$this->areFieldsEmpty(), Constants::ALL_FIELDS_REQUIRED);
        $this->assertCondition($this->isValidEmail(), Constants::INVALID_EMAIL);
        $this->assertCondition($this->userExists(), Constants::USER_DOES_NOT_EXIST);
        $this->assertCondition($this->isPasswordCorrect(), Constants::INCORRECT_PASSWORD);
    }

    private function assertCondition($condition, $message): void {
        if (!$condition) {
            HelperMethods::sendResponse(null, $message, 400);
        }
    }

    private function areFieldsEmpty(): bool {
        return empty($this->email) || empty($this->password);
    }

    private function isValidEmail(): bool {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    private function userExists(): bool {
        $query = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $query->execute([$this->email]);
        return $query->fetch() !== false;
    }

    private function isPasswordCorrect(): bool {
        $query = $this->db->prepare('SELECT password FROM users WHERE email = ?');
        $query->execute([$this->email]);
        $hashedPassword = $query->fetch()['password'];
        return password_verify($this->password, $hashedPassword);
    }

    private function getUser(): array {
        $query = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $query->execute([$this->email]);
        return $query->fetch();
    }

    private function generateToken(): string {
        try {
            $token = bin2hex(random_bytes(32));
            $query = $this->db->prepare('UPDATE users SET token = ? WHERE email = ?');
            $query->execute([$token, $this->email]);
            return $token;
        } catch (Exception $e) {
            HelperMethods::sendResponse(null, $e->getMessage(), 500);
        }
    }
}




