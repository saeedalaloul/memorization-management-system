<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{

    protected $fillable=[
        'student_id',
        'grade_id',
        'group_id',
        'teacher_id',
        'attendance_date',
        'attendance_status',
    ];
}
