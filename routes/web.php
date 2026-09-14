<?php

use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\EmployeeManagementController;
use App\Http\Controllers\Web\LocationManagementController; // Added missing import
use Illuminate\Support\Facades\Route;

// Redirect Halaman Utama
Route::get('/', fn() => redirect()->route('admin.login'));

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth Routes (Guest)
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Routes (Super Admin & Admin)
    Route::middleware(['auth', 'role:SUPER_ADMIN,ADMIN'])->group(function () {
        Route::get('/dashboard', [EmployeeManagementController::class, 'index'])->name('dashboard');

        // Employee Management
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [EmployeeManagementController::class, 'index'])->name('index');
            Route::post('/{id}/status', [EmployeeManagementController::class, 'updateStatus'])->name('updateStatus');
        });

        // Location Management
        Route::prefix('locations')->name('locations.')->group(function () {
            Route::get('/', [LocationManagementController::class, 'index'])->name('index');
            Route::post('/{id}/approve', [LocationManagementController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [LocationManagementController::class, 'reject'])->name('reject');
        });
    });
});