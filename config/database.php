<?php
class Database {
    private $host = "localhost";
    private $db_name = "latsar_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $host = getenv('DB_HOST') ?: $this->host;
            $db   = getenv('DB_NAME') ?: $this->db_name;
            $user = getenv('DB_USER') ?: $this->username;
            $pass = getenv('DB_PASS') ?: $this->password;

            $dsn = "mysql:host=" . $host . ";dbname=" . $db . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
