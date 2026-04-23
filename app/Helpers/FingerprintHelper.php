<?php

namespace App\Helpers;

class FingerprintHelper
{
    /**
     * Generate a stable numeric finger ID from a Firebase user ID.
     * Uses CRC32 hash bounded by FINGER_ID_MAX env variable.
     */
    public static function fingerIdFromUserId(string $userId): int
    {
        $max = (int) env('FINGER_ID_MAX', 9999999);
        return abs(crc32($userId)) % $max + 1;
    }
}
