<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController; // Direct import yang sebelumnya hilang
use App\Http\Controllers\Api\AttendanceController;
use Illuminate\Support\Facades\Route;

// Health Check Endpoint
Route::get('/health-check', fn() => response()->json([
    'status'    => 'success',
    'message'   => 'Attendance API is running smoothly',
    'timestamp' => now()->toIso8601String(),
]));

// API Version 1 Routes
Route::prefix('v1')->group(function () {

    // Public Auth Routes
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
    });

    // Protected Routes (Sanctum Authenticated)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth User Endpoints
        Route::prefix('auth')->controller(AuthController::class)->group(function () {
            Route::post('/logout', 'logout');
        });

        Route::get('/me', [AuthController::class, 'me']);

        // Location Management Endpoints
        Route::prefix('locations')->controller(LocationController::class)->group(function () {
            Route::post('/request', 'requestLocation');
            Route::get('/', 'myLocations');
            Route::post('/validate-radius', 'validateRadius');
        });

        Route::get('/attendance/schedule', [AttendanceController::class, 'schedule']);
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/history', [AttendanceController::class, 'history']);
    });
});
