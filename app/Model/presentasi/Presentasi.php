<?php

namespace App\Model\presentasi;

use App\Core\Model;
class Presentasi extends Model {
    protected $keterangan;
    static protected $table = 'presentasi';

    public function getAll() {
        $sql = "SELECT p.*,
                       (SELECT COUNT(*) FROM jadwal_presentasi jp WHERE jp.id_presentasi = p.id) as has_schedule
                FROM " . static::$table . " p";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        $stmt = $stmt->fetchAll();
        $data = [];
        foreach ($stmt as $result) {
            $nama = $this->getNameAndStambukFromPresentation($result['id_mahasiswa'])['nama_lengkap'];
            $stambuk =  $this->getNameAndStambukFromPresentation($result['id_mahasiswa'])['stambuk'];
            $berkas = $this->getPptAndMakalah($result['id_mahasiswa']);
            $data[] = [
                'id' => $result['id'],
                'id_mahasiswa' => $result['id_mahasiswa'],
                'nama' => $nama,
                'stambuk' => $stambuk,
                'judul' =>  $result['judul'],
                'is_accepted' => $result['is_accepted'] ?? 0,
                'is_revisi' => $result['is_revisi'] ?? 0,
                'has_schedule' => $result['has_schedule'] > 0,
                'berkas' => [
                    'ppt' => $berkas['ppt'],
                    'makalah' => $berkas['makalah']
                ]
            ];
        }
        return $data;
    }

    public function getAllAccStatus() {
        $sql = "SELECT * FROM " . static::$table . " WHERE is_accepted = 1";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        $stmt = $stmt->fetchAll();
        $data = [];
        foreach ($stmt as $result) {
            $nama = $this->getNameAndStambukFromPresentation($result['id_mahasiswa'])['nama_lengkap'];
            $stambuk =  $this->getNameAndStambukFromPresentation($result['id_mahasiswa'])['stambuk'];
            $berkas = $this->getPptAndMakalah($result['id_mahasiswa']);
            $data[] = [
                'id' => $result['id'],
                'id_mahasiswa' => $result['id_mahasiswa'],
                'nama' => $nama,
                'stambuk' => $stambuk,
                'judul' =>  $result['judul'],
                'berkas' => [
                    'ppt' => $berkas['ppt'],
                    'makalah' => $berkas['makalah']
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