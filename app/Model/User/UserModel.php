<?php

namespace App\Model\User;

use App\Core\Model;
use App\Core\Database;
use PDO;

class UserModel extends Model {
    protected static $table = 'user';
    protected $id;
    protected $username;
    protected $name;
    protected $stambuk;
    protected $password;
    protected $role;
    protected $created_at;
    protected $modified;

    public function __construct2($username, $stambuk, $password) {
        $this->username = $username;
        $this->stambuk = $stambuk;
        $this->password = $password;
    }
    public function __construct($id = null, $username = null, $stambuk = null, $password = null, $role = null, $created_at = null, $modified = null) {
        $this->id = $id;
        $this->username = $username;
        $this->stambuk = $stambuk;
        $this->password = $password;
        $this->role = $role;
        $this->created_at = $created_at;
        $this->modified = $modified;
    }

    

    public function save() {
        $query = "INSERT INTO user (username, stambuk, password) VALUES (?, ?, ?)";
        $pdo = self::getDB();
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->bindParam(2, $this->stambuk);
        $stmt->bindParam(3, $this->password);
        
        if ($stmt->execute()) {
            return $pdo->lastInsertId();
        }
        return false;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getName() {
        return $this->name;
    }

    public function getStambuk() {
        return $this->stambuk;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRole() {
        return $this->role;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getModified() {
        return $this->modified;
    }

    public static function getDB() {
        return Database::getInstance();
    }
    public static function findByStambuk($stambuk) {
        $query = "SELECT * FROM " . static::$table . " WHERE stambuk = :stambuk LIMIT 1";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':stambuk', $stambuk);
    
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            var_dump($stmt->errorInfo());
            return null;
        }
    }
    public function getUser($id) {
        $query = "SELECT * 
        FROM " . static::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        $user = [
            "username" => $result['username'],
            "stambuk" => $result['stambuk'],
            "password" => $result['password'],
            "role" => $result['role']
        ];
        return $user;
    }
    
    public function isStambukExists($stambuk) {
        $query = "SELECT COUNT(*) FROM " . static::$table . " WHERE stambuk = :stambuk";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':stambuk', $stambuk);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    public static function deleteUser($id) {
        $query = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function updateUser($id, $username, $password = null) {
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE " . static::$table . " SET username = :username, password = :password WHERE id = :id";
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);
        } else {
            $query = "UPDATE " . static::$table . " SET username = :username WHERE id = :id";
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':id', $id);
        }
        
        return $stmt->execute();
    }
}