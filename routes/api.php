<?php

use Illuminate\Support\Facades\Route;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Attendance API is running smoothly',
        'timestamp' => now()->toIso8601String(),
    ], 200);
});