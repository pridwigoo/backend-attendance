<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLocation;
use App\Services\Location\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function requestLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'address'   => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $location = EmployeeLocation::create([
            'user_id'       => $request->user()->id,
            'name'          => $request->name,
            'address'       => $request->address,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'radius_meters' => 500, // Maximum standard radius
            'status'        => 'PENDING',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan lokasi berhasil dikirim. Menunggu persetujuan Admin.',
            'data'    => $location,
        ], 201);
    }

    public function myLocations(Request $request)
    {
        $locations = EmployeeLocation::where('user_id', $request->user()->id)->get();

        return response()->json([
            'status' => 'success',
            'data'   => $locations,
        ], 200);
    }

    public function validateRadius(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:employee_locations,id',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
        ]);

        $location = EmployeeLocation::findOrFail($request->location_id);

        if ($location->status !== 'APPROVED') {
            return response()->json(['status' => 'error', 'message' => 'Lokasi belum disetujui Admin.'], 400);
        }

        $result = $this->locationService->isWithinRadius(
            $request->latitude,
            $request->longitude,
            $location->latitude,
            $location->longitude,
            $location->radius_meters
        );

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ], 200);
    }
}