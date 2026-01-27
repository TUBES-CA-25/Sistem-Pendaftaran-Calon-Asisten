<?php

namespace App\Services\User;

use App\Services\Shared\PathHelper;

class ProfileService
{
    /**
     * Generate initials from full name
     *
     * @param string|null $fullName Full name
     * @return string Two-letter initials
     */
    public static function generateInitials($fullName)
    {
        if (empty($fullName)) {
            return 'U';
        }

        $words = explode(' ', $fullName);
        if (count($words) >= 2) {
            // Take first letter of first two words
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            // Take first two letters of single word
            return strtoupper(substr($fullName, 0, 2));
        }
    }

    /**
     * Format complete profile display data
     *
     * @param array $biodata Biodata array
     * @param array $user User session array
     * @param string|null $photo Photo filename
     * @return array Formatted profile display data
     */
    public static function formatProfileDisplay($biodata, $user, $photo)
    {
        $nama = $biodata['namaLengkap'] ?? $user['username'] ?? 'User';
        $hasValidPhoto = PathHelper::hasValidPhoto($photo);

        return [
            'hasValidPhoto' => $hasValidPhoto,
            'photoPath' => PathHelper::getUserPhotoPath($photo),
            'initials' => self::generateInitials($nama),
            'displayName' => $nama
        ];
    }
}
