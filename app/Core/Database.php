<?php

namespace App\Core;
use \PDO;
class Database {
    /**
     * Koneksi PDO bersama (singleton).
     *
     * Diberi tipe `?PDO` supaya IDE/static analyzer tahu isinya objek PDO —
     * tanpa ini, pemanggilan seperti self::$instance->prepare() di bawah
     * ditandai "method tidak dikenal" karena tipenya tidak diketahui.
     */
    private static ?PDO $instance = null;

    private function __construct() {
        // Private constructor to prevent direct instantiation
    }

    /** Buat koneksi bila belum ada. Selalu mengembalikan PDO, atau berhenti. */
    private static function con(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = DB_CONNECTION . ':host=' . DB_HOST . ';port=' . PORT . ';dbname=' . DB_NAME;
                self::$instance = new PDO($dsn, DB_USER, DB_PASS);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    public static function getInstance(): PDO {
        return self::con();
    }

    public static function query(string $query, array $data = []): \PDOStatement {
        $stmt = self::con()->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
}
