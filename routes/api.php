<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FaceRegisterController;
use App\Http\Controllers\Api\LocationController;
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
            Route::get('/me', 'me');
        });

        // Face Recognition Endpoints
        Route::prefix('face')->controller(FaceRegisterController::class)->group(function () {
            Route::post('/register', 'registerFace');
        });

        // Location Management Endpoints
        Route::prefix('locations')->controller(LocationController::class)->group(function () {
            Route::post('/request', 'requestLocation');
            Route::get('/', 'myLocations');
            Route::post('/validate-radius', 'validateRadius');
        });

        // Attendance Endpoints
        Route::prefix('attendance')->controller(AttendanceController::class)->group(function () {
            Route::get('/schedule', 'schedule');
            Route::post('/check-in', 'checkIn');
            Route::post('/check-out', 'checkOut');
            Route::get('/history', 'history');
        });
    });
});