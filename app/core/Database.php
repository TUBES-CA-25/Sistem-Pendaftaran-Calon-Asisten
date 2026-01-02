<?php

<<<<<<< HEAD
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $db_name = DB_NAME;

    private $dbh;
    private $stmt;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;

        $option = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $option);
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind($param, $value, $type = null)
    {
        if( is_null($type) ) {
            switch( true ) {
                case is_int($value) :
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value) :
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value) :
                    $type = PDO::PARAM_NULL;
                    break;
                default :
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute()
    {
        $this->stmt->execute();
    }

    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
=======
namespace App\Core;
use \PDO;
class Database {
    private static $pdo;
    private static $DB_CONNECTION = "mysql";
    private static $DB_HOST = "localhost";
    private static $DB_USER = "root";
    private static $DB_PORT = 3306;
    private static $DB_NAME = "DB_TUBES";
    private static $DB_PASS = "";

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
>>>>>>> origin/raihn
    }
}
