<?php

namespace App\Services\Attendance;

use App\Models\Attendance;
use App\Models\WorkSchedule;
use App\Services\Location\LocationService;
use Carbon\Carbon;
use Exception;

class AttendanceService
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function checkIn($user, array $data)
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // 1. Cek apakah sudah check-in hari ini
        $existing = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if ($existing && $existing->check_in) {
            throw new Exception('Anda sudah melakukan Check-in hari ini.');
        }

        // 2. Ambillah Jadwal Kerja (Default ID 1 untuk MVP)
        $schedule = WorkSchedule::first();
        $startTime = Carbon::parse($schedule->start_time);
        $toleranceTime = $startTime->copy()->addMinutes($schedule->tolerance_minutes);
        $now = Carbon::parse($currentTime);

        // Status Keterlambatan
        $status = $now->greaterThan($toleranceTime) ? 'LATE' : 'PRESENT';

        // 3. Simpan Transaksi Attendance
        return Attendance::create([
            'user_id'                  => $user->id,
            'location_id'              => $data['location_id'],
            'date'                     => $today,
            'check_in'                 => $currentTime,
            'check_in_latitude'        => $data['latitude'],
            'check_in_longitude'       => $data['longitude'],
            'check_in_distance'        => $data['distance'],
            'check_in_accuracy'        => $data['accuracy'] ?? null,
            'face_verification_status' => $data['face_verification_status'] ?? ($data['face_verified'] ?? 'VERIFIED'),
            'status'                   => $status,
            'notes'                    => $data['notes'] ?? null,
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

        // Update status jika pulang lebih awal (Left Early) dan status sebelumnya BUKAN LATE
        $status = $attendance->status;
        if ($now->lessThan($endTime) && $status !== 'LATE') {
            $status = 'LEFT_EARLY';
        }

        $attendance->update([
            'check_out'           => $currentTime,
            'check_out_latitude'  => $data['latitude'],
            'check_out_longitude' => $data['longitude'],
            'check_out_distance'  => $data['distance'],
            'check_out_accuracy'  => $data['accuracy'] ?? null,
            'status'              => $status,
        ]);

        return $attendance;
    }
}