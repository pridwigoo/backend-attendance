<?php

use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\EmployeeManagementController;
use App\Http\Controllers\Web\LocationManagementController;
use Illuminate\Support\Facades\Route;

// Redirect Halaman Utama
Route::get('/', fn() => redirect()->route('admin.login'));

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth Routes (Guest)
    Route::controller(AdminAuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('login.submit');
        Route::post('/logout', 'logout')->name('logout');
    });

    // Protected Routes (Super Admin & Admin)
    Route::middleware(['auth', 'role:SUPER_ADMIN,ADMIN'])->group(function () {
        Route::get('/dashboard', [EmployeeManagementController::class, 'index'])->name('dashboard');

        // Employee Management
        Route::prefix('employees')->name('employees.')->controller(EmployeeManagementController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/{id}/status', 'updateStatus')->name('updateStatus');
        });

        // Location Management
        Route::prefix('locations')->name('locations.')->controller(LocationManagementController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
        });
    });
});
