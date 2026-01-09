<?php

namespace App\Core;
use \PDO;
class Database {
    private static $pdo;
    private static $DB_CONNECTION = DB_CONNECTION;
    private static $DB_HOST = DB_HOST;
    private static $DB_USER = DB_USER;
    private static $DB_PORT = PORT;
    private static $DB_NAME = DB_NAME;
    private static $DB_PASS = DB_PASS;
    
    private static function con() {
        $DB = self::$DB_CONNECTION . ':host=' . 
        self::$DB_HOST . ';port=' . 
        self::$DB_PORT . ';dbname=' . 
        self::$DB_NAME;
        
        self::$pdo = new \PDO($DB,self::$DB_USER,self::$DB_PASS);
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
