<?php

namespace App\Services\Shared;

class PathHelper
{
    private static $baseImagePath = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/';
    private static $defaultPhoto = 'default.png';

    /**
     * Get full path for user photo
     *
     * @param string|null $filename Photo filename
     * @return string Full path to photo or default photo
     */
    public static function getUserPhotoPath($filename)
    {
        if (empty($filename) || $filename === self::$defaultPhoto) {
            return self::$baseImagePath . self::$defaultPhoto;
        }

        // If already has path, return as is
        if (strpos($filename, '/') !== false) {
            return $filename;
        }

        return self::$baseImagePath . $filename;
    }

    /**
     * Check if photo is valid (not default)
     *
     * @param string|null $filename Photo filename
     * @return bool True if valid photo exists
     */
    public static function hasValidPhoto($filename)
    {
        return !empty($filename) && $filename !== self::$defaultPhoto;
    }
}
