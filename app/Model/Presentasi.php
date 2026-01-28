<?php

namespace App\Model;

use App\Core\Model;
class Presentasi extends Model {
    protected $keterangan;
    static protected $table = 'presentasi';

    public function getAll() {
        $sql = "SELECT p.*, m.nama_lengkap, m.stambuk,
                       (SELECT COUNT(*) FROM jadwal_presentasi jp WHERE jp.id_presentasi = p.id) as has_schedule
                FROM " . static::$table . " p
                JOIN mahasiswa m ON p.id_mahasiswa = m.id
                ORDER BY p.id DESC";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'id' => $result['id'],
                'id_mahasiswa' => $result['id_mahasiswa'],
                'nama' => $result['nama_lengkap'],
                'stambuk' => $result['stambuk'],
                'judul' =>  $result['judul'],
                'is_accepted' => $result['is_accepted'] ?? 0,
                'is_revisi' => $result['is_revisi'] ?? 0,
                'has_schedule' => $result['has_schedule'] > 0,
                'berkas' => [
                    'ppt' => $result['ppt'],
                    'makalah' => $result['makalah']
                ]
            ];
        }
        return $data;
    }

    public function getAllAccStatus() {
        $sql = "SELECT p.*, m.nama_lengkap, m.stambuk
                FROM " . static::$table . " p
                JOIN mahasiswa m ON p.id_mahasiswa = m.id
                WHERE p.is_accepted = 1
                ORDER BY p.id DESC";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'id' => $result['id'],
                'id_mahasiswa' => $result['id_mahasiswa'],
                'nama' => $result['nama_lengkap'],
                'stambuk' => $result['stambuk'],
                'judul' =>  $result['judul'],
                'berkas' => [
                    'ppt' => $result['ppt'],
                    'makalah' => $result['makalah']
                ]
            ];
        }
        return $data;
    }


    public function getAbsensi() {
        $sql = "SELECT absensi,id_mahasiswa FROM " . static::$table;
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        $stmt = $stmt->fetchAll();
        $nama = $this->getNameAndStambukFromPresentation($stmt['id_mahasiswa'])['nama_lengkap'];
        $stambuk = $this->getNameAndStambukFromPresentation($stmt['id_mahasiswa'])['stambuk'];
        return [
            "nama" => $nama,
            "stambuk" => $stambuk,
            "absensi" => $stmt['absensi']
        ];
    }
    private function getNameAndStambukFromPresentation($id) {
        $sql = "SELECT stambuk,nama_lengkap from 
        mahasiswa where id = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
   
    private function getPptAndMakalah($id) {
        $sql = "SELECT ppt,makalah from " . 
        static::$table . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateJudulStatus($id, $status = 1) {
        // Status: 0 = pending, 1 = accepted, 2 = rejected
        $is_revisi = ($status == 2) ? 1 : 0;
        $sql = "UPDATE " . static::$table . " SET is_accepted = :status, is_revisi = :is_revisi WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':is_revisi', $is_revisi);
        return $stmt->execute();
    }

    public function updateIsRevisiAndKeterangan($id,$keterangan) {
        $sql = "UPDATE " . static::$table . " SET is_revisi = 1, is_accepted = 0, keterangan = ? WHERE id= ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $keterangan);
        $stmt->bindParam(2, $id);
        return $stmt->execute();
    }
}
