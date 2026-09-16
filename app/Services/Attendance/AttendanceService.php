<?php

namespace App\Services\Attendance;

use App\Models\Attendance;
use App\Models\WorkSchedule;
use App\Services\Location\LocationService;
use App\Services\FaceRecognition\FaceRecognitionService;
use Carbon\Carbon;
use Exception;

class AttendanceService
{
    protected $locationService;
    protected $faceService;

    public function __construct(LocationService $locationService, FaceRecognitionService $faceService)
    {
        $this->locationService = $locationService;
        $this->faceService = $faceService;
    }

    public function processIntegratedCheckIn($user, array $data, $uploadedFaceFile = null)
    {
        // Rule 1: User Status Check
        if ($user->status !== 'ACTIVE') {
            throw new Exception('Akun Anda tidak aktif. Tidak dapat melakukan absensi.');
        }

        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // Rule 2: Duplicate Check-in Guard
        $existing = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if ($existing && $existing->check_in) {
            throw new Exception('Anda sudah melakukan Check-in hari ini.');
        }

        // Rule 3: Schedule Check
        $schedule = WorkSchedule::first();
        if (!$schedule) {
            throw new Exception('Jadwal kerja belum dikonfigurasi oleh Admin.');
        }

        // Rule 4 & 5 & 6: Location & GPS Radius Check (Max 500m)
        $location = \App\Models\EmployeeLocation::find($data['location_id']);
        if (!$location || $location->user_id !== $user->id || $location->status !== 'APPROVED') {
            throw new Exception('Lokasi yang dipilih tidak valid atau belum disetujui Admin.');
        }

        $radiusCheck = $this->locationService->isWithinRadius(
            $data['latitude'],
            $data['longitude'],
            $location->latitude,
            $location->longitude,
            $location->radius_meters
        );

        if (!$radiusCheck['is_valid']) {
            throw new Exception("Check-in ditolak! Jarak Anda ({$radiusCheck['distance']}m) melebihi batas radius ({$radiusCheck['max_radius']}m).");
        }

        // Rule 7 & 8: Face Verification & Liveness Check
        $faceVerification = $this->faceService->verifyAttendanceFace($user, $uploadedFaceFile);
        if (!$faceVerification['verified']) {
            throw new Exception('Verifikasi Wajah Gagal: ' . $faceVerification['message']);
        }

        // Hitung Keterlambatan berdasarkan server time
        $startTime = Carbon::parse($schedule->start_time);
        $toleranceTime = $startTime->copy()->addMinutes($schedule->tolerance_minutes);
        $now = Carbon::parse($currentTime);
        $status = $now->greaterThan($toleranceTime) ? 'LATE' : 'PRESENT';

        // Simpan foto bukti absensi ke storage
        $attendancePhotoPath = $uploadedFaceFile->store('attendance_proofs', 'public');

        // Save Attendance
        return Attendance::create([
            'user_id'                  => $user->id,
            'location_id'              => $location->id,
            'date'                     => $today,
            'check_in'                 => $currentTime,
            'check_in_latitude'        => $data['latitude'],
            'check_in_longitude'       => $data['longitude'],
            'check_in_distance'        => $radiusCheck['distance'],
            'check_in_accuracy'        => $data['accuracy'] ?? 0,
            'face_verification_status' => 'VERIFIED',
            'status'                   => $status,
            'notes'                    => 'Check-in terintegrasi sukses via GPS & Face Verification. Proof: ' . $attendancePhotoPath,
        ]);
    }

    public function checkOut($user, array $data)
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance || !$attendance->check_in) {
            throw new Exception('Anda belum melakukan Check-in hari ini.');
        }

        if ($attendance->check_out) {
            throw new Exception('Anda sudah melakukan Check-out hari ini.');
        }

        $schedule = WorkSchedule::first();
        $endTime = Carbon::parse($schedule->end_time);
        $now = Carbon::parse($currentTime);

        $status = $attendance->status;
        if ($now->lessThan($endTime) && $status !== 'LATE') {
            $status = 'LEFT_EARLY';
        }

        $attendance->update([
            'check_out'           => $currentTime,
            'check_out_latitude'  => $data['latitude'],
            'check_out_longitude' => $data['longitude'],
            'check_out_distance'  => 0,
            'status'              => $status,
        ]);

        return $attendance;
    }
}
