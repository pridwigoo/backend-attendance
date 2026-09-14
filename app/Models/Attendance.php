<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'date',
        'check_in',
        'check_out',
        'break_start',
        'break_end',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_distance',
        'check_in_accuracy',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_distance',
        'check_out_accuracy',
        'face_verification_status',
        'status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(EmployeeLocation::class, 'location_id');
    }
}