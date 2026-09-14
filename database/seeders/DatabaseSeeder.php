<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'employee_id' => 'ADMIN-001',
            'name'        => 'Super Admin',
            'email'       => 'admin@attendance.com',
            'phone'       => '08111111111',
            'password'    => Hash::make('admin12345'),
            'role'        => 'SUPER_ADMIN',
            'status'      => 'ACTIVE',
        ]);

        // 2. Dummy Active Employee
        User::create([
            'employee_id' => 'EMP-001',
            'name'        => 'Employee Active Test',
            'email'       => 'employee@test.com',
            'phone'       => '08123456789',
            'password'    => Hash::make('password123'),
            'role'        => 'EMPLOYEE',
            'status'      => 'ACTIVE',
        ]);

        // 3. Dummy Pending Employee
        User::create([
            'employee_id' => 'EMP-002',
            'name'        => 'Budi Pending',
            'email'       => 'budi@test.com',
            'phone'       => '08129999888',
            'password'    => Hash::make('password123'),
            'role'        => 'EMPLOYEE',
            'status'      => 'PENDING',
        ]);

        // Tambahkan di dalam fungsi run():
        \App\Models\WorkSchedule::create([
            'name' => 'Reguler Office Hour',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
            'tolerance_minutes' => 15,
            'work_days' => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
        ]);
    }
}
