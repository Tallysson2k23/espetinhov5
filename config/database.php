<?php

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $host = "localhost";
        $port = "5432";
        $dbname = "espetinhov5";
        $user = "postgres";
        $password = "159357";

        try {
            $this->connection = new PDO(
                "pgsql:host=$host;port=$port;dbname=$dbname",
                $user,
                $password
            );

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}