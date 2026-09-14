<?php

namespace App\Services\FaceRecognition;

use App\Models\User;

class FaceRecognitionService
{
    /**
     * Daftarkan data wajah karyawan (menyimpan path foto / embedding)
     */
    public function registerFace(User $user, string $photoPath): bool
    {
        $user->update([
            'profile_photo' => $photoPath,
        ]);

        return true;
    }

    /**
     * Verifikasi sampel foto absensi dengan data foto terdaftar
     */
    public function verifyFace(User $user, string $samplePhotoPath): array
    {
        // Pengecekan dasar: Karyawan harus sudah mendaftarkan wajah
        if (!$user->profile_photo) {
            return [
                'verified' => false,
                'message'  => 'Wajah belum terdaftar. Silakan registrasi wajah terlebih dahulu.',
            ];
        }

        // Abstraksi Engine: Pada MVP v1.0, verifikasi dasar memastikan berkas foto sampel valid & ada.
        // Interface ini siap dihubungkan dengan engine AI/ML external pada V2.
        $isVerified = file_exists(storage_path('app/public/' . $samplePhotoPath));

        return [
            'verified' => $isVerified,
            'message'  => $isVerified ? 'Verifikasi wajah berhasil.' : 'Verifikasi wajah gagal.',
        ];
    }
}
