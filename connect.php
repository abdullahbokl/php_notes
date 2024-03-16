<?php

class DataBase {

    static private string $dsn = "mysql:host=127.0.0.1;dbname=notesapp";
    static private string $username = 'root';
    static private string $password = '';
    static private PDO $db;

    static private function connect() {
        try {
            $db = new PDO(self::$dsn, self::$username, self::$password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Connection failed => ' . $e->getMessage();
            exit;
        }
        return $db;
    }

    static public function getInstance(): PDO {
        if (empty(self::$db)) {
            self::$db = self::connect();
        }
        return self::$db;
    }
}
