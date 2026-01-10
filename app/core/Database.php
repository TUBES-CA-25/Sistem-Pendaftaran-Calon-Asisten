<?php

namespace App\Core;
use \PDO;
class Database {
    private static $pdo;
    
    private static function con() {
        $DB = DB_CONNECTION . ':host=' . 
        DB_HOST . ';port=' . 
        PORT . ';dbname=' . 
        DB_NAME;
        
        self::$pdo = new \PDO($DB,DB_USER,DB_PASS);
    }

    public static function getInstance() {
        self::con();
        return self::$pdo;
    }
    public static function query($query,$data = []) {
        self::con();

        $stmt = self::$pdo->prepare($query);
        $stmt->execute($data);

        self::$pdo = null;

        return $stmt;
    }
}
