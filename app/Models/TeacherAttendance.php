<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{

    protected $fillable=[
        'teacher_id',
        'grade_id',
        'attendance_date',
        'attendance_status',
    ];
}
