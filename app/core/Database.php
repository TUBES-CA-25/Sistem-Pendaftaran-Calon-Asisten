<?php

namespace App\Core;
use \PDO;
class Database {
    private static $instance = null;

    private function __construct() {
        // Private constructor to prevent direct instantiation
    }

    private static function con() {
        if (self::$instance === null) {
            try {
                $dsn = DB_CONNECTION . ':host=' . DB_HOST . ';port=' . PORT . ';dbname=' . DB_NAME;
                self::$instance = new \PDO($dsn, DB_USER, DB_PASS);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
    }

    public static function getInstance() {
        self::con();
        return self::$instance;
    }

    public static function query($query, $data = []) {
        self::con();
        $stmt = self::$instance->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
}
