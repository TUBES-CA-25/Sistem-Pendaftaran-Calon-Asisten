<?php

namespace App\Services\Admin;

use App\Services\Shared\PathHelper;

class ParticipantDataService
{
    /**
     * Format participant data for view display
     *
     * @param array $rawData Raw participant data from database
     * @return array Formatted participant data with photoPath and statusBadge
     */
    public static function formatParticipantForView($rawData)
    {
        $formatted = $rawData;

        // Add photo path
        $photoName = $rawData['berkas']['foto'] ?? 'default.png';
        $formatted['photoPath'] = PathHelper::getUserPhotoPath($photoName);

        // Add status badge
        $acceptedStatus = $rawData['berkas']['accepted'] ?? null;
        $formatted['statusBadge'] = StatusFormatterService::getBerkasStatusBadge($acceptedStatus);

        return $formatted;
    }
}
