<?php

namespace App\Model\Pengumuman;

use App\Core\Model;

class PengumumanAdmin extends Model {
    protected static $table = 'pengumuman'; // Nama tabel di DB
    
    // Properti sesuai kolom database
    protected $id;
    protected $judul_pengumuman;
    protected $pengumuman; // Ingat, nama kolom di DB Anda 'pengumuman', bukan 'isi_pengumuman'
    protected $created_at;
    protected $modified_at; // waktu ketika mengubah atau update pengumuman yang sudah di publis

    public function __construct( // <- 
        $judul_pengumuman = null,
        $pengumuman = null
    ) {
        $this->judul_pengumuman = $judul_pengumuman;
        $this->pengumuman = $pengumuman;
    }

    // Mengambil semua data (Mirip getAll di Notification)
    public function getAll() {
        // Menggunakan 'created_at' untuk sorting
        $query = "SELECT * FROM " . static::$table . " ORDER BY created_at DESC";
        
        // Panggil DB connection pakai gaya Anda (self::getDB())
        $stmt = self::getDB()->prepare($query);
        $stmt->execute();
        
        // FetchAll langsung
        return $stmt->fetchAll();
    }

    // Insert Data (Mirip insert di Notification)
    public function insert(PengumumanAdmin $data) {
        // Kolom di DB: judul_pengumuman, pengumuman
        $query = "INSERT INTO " . static::$table . " (judul_pengumuman, pengumuman) VALUES (:judul, :isi)";
        
        $stmt = self::getDB()->prepare($query);
        
        // Bind parameter
        $stmt->bindParam(':judul', $data->judul_pengumuman);
        $stmt->bindParam(':isi', $data->pengumuman);
        
        return $stmt->execute();
    }

    // Hapus Data (Tambahan fitur yang tidak ada di Notification tapi wajib di Pengumuman)
    public function delete($id) {
        $query = "DELETE FROM " . static::$table . " WHERE id = :id";
        
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }


    // --- TAMBAHAN BARU DI BAWAH INI ---

    // 1. Ambil 1 data berdasarkan ID (Untuk Form Edit)
    public function getById($id) {
        $query = "SELECT * FROM " . static::$table . " WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // 2. Update Data (Otomatis update modified_at)
    public function update(PengumumanAdmin $data) {
        $query = "UPDATE " . static::$table . " SET 
                  judul_pengumuman = :judul, 
                  pengumuman = :isi, 
                  modified_at = NOW() 
                  WHERE id = :id";
        
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':judul', $data->judul_pengumuman);
        $stmt->bindParam(':isi', $data->pengumuman);
        $stmt->bindParam(':id', $data->id);
        
        return $stmt->execute();
    }




}