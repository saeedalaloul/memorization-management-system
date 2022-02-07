<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'teacher_id',
        'grade_id',
        'attendance_date',
        'attendance_status',
    ];
}
