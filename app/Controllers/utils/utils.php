<?php

namespace App\Controllers\Utils;

/**
 * Utility class berisi helper functions untuk aplikasi
 */
class Utils
{
    /**
     * Sanitize input string untuk mencegah XSS
     * @param string $data
     * @return string
     */
    public static function sanitize(string $data): string
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    /**
     * Validasi format email
     * @param string $email
     * @return bool
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Format tanggal ke format Indonesia
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function formatDate(string $date, string $format = 'd F Y'): string
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $timestamp = strtotime($date);
        $day = date('d', $timestamp);
        $month = (int)date('m', $timestamp);
        $year = date('Y', $timestamp);

        return "$day {$bulan[$month]} $year";
    }

    /**
     * Generate random string untuk token
     * @param int $length
     * @return string
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Check apakah request adalah AJAX
     * @return bool
     */
    public static function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Redirect ke URL tertentu
     * @param string $url
     * @return void
     */
    public static function redirect(string $url): void
    {
        header("Location: " . APP_URL . "/" . $url);
        exit();
    }

    /**
     * Flash message untuk session
     * @param string $type (success, error, warning, info)
     * @param string $message
     * @return void
     */
    public static function setFlashMessage(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get flash message dan hapus dari session
     * @return array|null
     */
    public static function getFlashMessage(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Format ukuran file ke human readable
     * @param int $bytes
     * @return string
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Validate file upload
     * @param array $file $_FILES array
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Maximum file size in bytes
     * @return array ['valid' => bool, 'error' => string|null]
     */
    public static function validateFileUpload(array $file, array $allowedTypes, int $maxSize): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Upload gagal'];
        }

        if (!in_array($file['type'], $allowedTypes)) {
            return ['valid' => false, 'error' => 'Tipe file tidak diizinkan'];
        }

        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'Ukuran file terlalu besar'];
        }

        return ['valid' => true, 'error' => null];
    }
}