<?php

namespace App\Controllers\Concerns;

/**
 * Resolusi path foto profil user/admin beserta fallback-nya.
 *
 * Dipisah dari HomeController agar tiap berkas tetap pendek dan mudah dirawat.
 * Isi method TIDAK diubah - hanya dipindahkan.
 */
trait ManagesUserPhoto
{
    public static function getUserPhotoPath($filename)
    {
        $defaultPhoto = 'default.png';
        $webBasePath = '/Sistem-Pendaftaran-Calon-Asisten/res/';
        $docRoot = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/';
        $defaultPhotoUrl = '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png';

        if (empty($filename) || $filename === $defaultPhoto) {
            // Return new default photo location
            return $defaultPhotoUrl;
        }

        if (strpos($filename, '/') !== false) {
            return $filename;
        }

        // Check imageUser directory first (berkas uploads - priority)
        if (file_exists($docRoot . 'profile/' . $filename)) {
            return $webBasePath . 'profile/' . $filename . '?v=' . time();
        }

        // Check profile directory as fallback
        if (file_exists($docRoot . 'profile/' . $filename)) {
            return $webBasePath . 'profile/' . $filename . '?v=' . time(); // Add cache busting
        }

        // Fallback to default if not found in either
        return $defaultPhotoUrl;
    }

    /**
     * Check if photo is valid (not default)
     */

    private function hasValidPhoto($filename)
    {
        return !empty($filename) && $filename !== 'default.png';
    }

    /**
     * Generate initials from full name
     */

    private function generateInitials($fullName)
    {
        if (empty($fullName)) {
            return 'U';
        }

        $words = explode(' ', $fullName);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            return strtoupper(substr($fullName, 0, 2));
        }
    }

    /**
     * Format complete profile display data
     */

    public static function getAdminPhoto($userId) {
        $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/profile/';
        $webPath = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/';
        
        $extensions = ['png', 'jpg', 'jpeg'];
        
        clearstatcache();

        foreach ($extensions as $ext) {
            $filename = "admin_{$userId}.{$ext}";
            if (file_exists($baseDir . $filename)) {
                return $webPath . $filename . '?v=' . time();
            }
        }

        return '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/Rectangle.png';
    }
}
