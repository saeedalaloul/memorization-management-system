<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'student_id',
        'grade_id',
        'group_id',
        'teacher_id',
        'attendance_date',
        'attendance_status',
    ];
}
