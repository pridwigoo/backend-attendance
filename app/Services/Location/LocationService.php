<?php

namespace App\Services\Location;

class LocationService
{
    /**
     * Hitung jarak 2 koordinat GPS menggunakan Rumus Haversine (dalam satuan Meter)
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2); // Return meter dengan 2 desimal
    }

    /**
     * Validasi apakah lokasi berada dalam radius maksimum (default 500m)
     */
    public function isWithinRadius($userLat, $userLon, $targetLat, $targetLon, $maxRadius = 500): array
    {
        $distance = $this->calculateDistance($userLat, $userLon, $targetLat, $targetLon);
        $isValid = $distance <= $maxRadius;

        return [
            'is_valid'   => $isValid,
            'distance'   => $distance,
            'max_radius' => $maxRadius,
        ];
    }
}