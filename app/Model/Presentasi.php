<?php

namespace App\Model;

use App\Core\Model;
class Presentasi extends Model {
    protected $keterangan;
    static protected $table = 'presentasi';

    /**
     * Daftar pengajuan judul untuk admin — SATU baris per mahasiswa.
     *
     * Sejak riwayat pengajuan disimpan sebagai baris terpisah (judul yang
     * ditolak tetap tersimpan), tabel ini bisa berisi beberapa baris untuk
     * mahasiswa yang sama. Tanpa pembatas di bawah, nama mahasiswa muncul
     * berulang di halaman admin — satu baris untuk tiap judul yang pernah
     * diajukan. Admin hanya perlu melihat pengajuan TERBARU.
     */
    public function getAll() {
        $sql = "SELECT p.*, m.nama_lengkap, m.stambuk,
                       (SELECT COUNT(*) FROM jadwal_presentasi jp WHERE jp.id_presentasi = p.id) as has_schedule,
                       (SELECT foto FROM berkas_mahasiswa WHERE id_mahasiswa = m.id ORDER BY id DESC LIMIT 1) as foto
                FROM " . static::$table . " p
                JOIN mahasiswa m ON p.id_mahasiswa = m.id
                WHERE p.id = (SELECT MAX(p2.id) FROM " . static::$table . " p2
                              WHERE p2.id_mahasiswa = p.id_mahasiswa)
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
                'foto' => $result['foto'] ?? null,
                'berkas' => [
                    'ppt' => $result['ppt'],
                    'makalah' => $result['makalah']
                ]
            ];
        }
        return $data;
    }

    /**
     * Daftar judul yang DITERIMA — juga satu baris per mahasiswa (yang terbaru),
     * dengan alasan yang sama seperti getAll().
     */
    public function getAllAccStatus() {
        $sql = "SELECT p.*, m.nama_lengkap, m.stambuk,
                       (SELECT foto FROM berkas_mahasiswa WHERE id_mahasiswa = m.id ORDER BY id DESC LIMIT 1) as foto
                FROM " . static::$table . " p
                JOIN mahasiswa m ON p.id_mahasiswa = m.id
                WHERE p.is_accepted = 1
                  AND p.id = (SELECT MAX(p2.id) FROM " . static::$table . " p2
                              WHERE p2.id_mahasiswa = p.id_mahasiswa)
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
                'foto' => $result['foto'] ?? null,
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

    /**
     * Terima/tolak judul.
     *
     * ORDER BY id DESC LIMIT 1 penting: sejak riwayat pengajuan disimpan
     * sebagai baris terpisah, tanpa pembatas ini SELURUH riwayat mahasiswa
     * ikut berubah statusnya. Yang dinilai hanya pengajuan terbaru.
     *
     * Status: 1 = diterima, 2 = ditolak.
     */
    public function updateJudulStatus($id, $status = 1) {
        $is_revisi = ($status == 2) ? 1 : 0;
        $sql = "UPDATE " . static::$table . "
                SET is_accepted = :status, is_revisi = :is_revisi
                WHERE id_mahasiswa = :id
                ORDER BY id DESC LIMIT 1";
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
