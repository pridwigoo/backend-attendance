<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\WorkSchedule;
use App\Services\Attendance\AttendanceService;
use App\Services\Location\LocationService;
use Illuminate\Http\Request;
use Exception;

class AttendanceController extends Controller
{
    protected $attendanceService;
    protected $locationService;

    public function __construct(AttendanceService $attendanceService, LocationService $locationService)
    {
        $this->attendanceService = $attendanceService;
        $this->locationService = $locationService;
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:employee_locations,id',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
        ]);

        try {
            // Evaluasi Jarak Radius (Location Service)
            $location = \App\Models\EmployeeLocation::findOrFail($request->location_id);
            $radiusCheck = $this->locationService->isWithinRadius(
                $request->latitude,
                $request->longitude,
                $location->latitude,
                $location->longitude,
                $location->radius_meters
            );

            if (!$radiusCheck['is_valid']) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Posisi Anda ({$radiusCheck['distance']}m) melebihi radius lokasi yang diizinkan ({$radiusCheck['max_radius']}m).",
                ], 400);
            }

            $attendance = $this->attendanceService->checkIn($request->user(), array_merge($request->all(), [
                'distance' => $radiusCheck['distance']
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Check-in Berhasil',
                'data' => $attendance
            ], 200);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $attendance = $this->attendanceService->checkOut($request->user(), array_merge($request->all(), [
                'distance' => 0
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Check-out Berhasil',
                'data' => $attendance
            ], 200);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function history(Request $request)
    {
        $history = Attendance::with('location')
            ->where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $history
        ], 200);
    }

    public function schedule()
    {
        $schedule = WorkSchedule::first();
        return response()->json([
            'status' => 'success',
            'data' => $schedule
        ], 200);
    }
}