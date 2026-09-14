<?php

use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\EmployeeManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['auth', 'role:SUPER_ADMIN,ADMIN'])->group(function () {
        Route::get('/dashboard', [EmployeeManagementController::class, 'index'])->name('admin.dashboard');
        Route::get('/employees', [EmployeeManagementController::class, 'index'])->name('admin.employees.index');
        Route::post('/employees/{id}/status', [EmployeeManagementController::class, 'updateStatus'])->name('admin.employees.updateStatus');
    });
});