<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class StudentSunnahAttendance extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'datetime',
        'status',
    ];

    const PRESENCE_STATUS = "presence";
    const ABSENCE_STATUS = "absence";
    const LATE_STATUS = "late";
    const AUTHORIZED_STATUS = "authorized";

    public static function status(){
        return [
            self::PRESENCE_STATUS => 'حضور',
            self::ABSENCE_STATUS => 'غياب',
            self::LATE_STATUS => 'متأخر',
            self::AUTHORIZED_STATUS => 'مأذون',
        ];
    }
}
