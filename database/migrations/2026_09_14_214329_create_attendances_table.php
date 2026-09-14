<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('employee_locations')->onDelete('set null');
            $table->date('date');

            // Jam Transaksi
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();

            // Data Geolocation Check-in
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_in_distance', 8, 2)->nullable(); // Jarak Haversine saat check-in
            $table->decimal('check_in_accuracy', 8, 2)->nullable();

            // Data Geolocation Check-out
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->decimal('check_out_distance', 8, 2)->nullable();
            $table->decimal('check_out_accuracy', 8, 2)->nullable();

            // Verification & Status
            $table->enum('face_verification_status', ['VERIFIED', 'FAILED', 'SKIPPED'])->default('SKIPPED');
            $table->enum('status', ['PRESENT', 'LATE', 'LEFT_EARLY', 'ABSENT', 'INVALID'])->default('PRESENT');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
