<?php

namespace App\Model\presentasi;

use App\Core\Model;

class Ruangan extends Model {


    static protected $table = 'ruangan';

    public function getAll() {
        $sql = "SELECT * FROM " . static::$table;
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function insertRuangan($nama) {
        $sql = "INSERT INTO " . static::$table . " (nama) VALUES (?) ";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $nama);
        $stmt->execute();
    }
    public function deleteRuangan($id) {
        $sql = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
    }
    public function updateRuangan($id,$nama) {
        $sql = "UPDATE " . static::$table . " SET nama = ?, modified = ? WHERE id = ?";
        $date = date('Y-m-d H:i:s');
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $nama);
        $stmt->bindParam(2, $date);
        $stmt->bindParam(3, $id);
        $stmt->execute();
    }
}