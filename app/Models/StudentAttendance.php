<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'datetime',
        'status',
    ];

    public const PRESENCE_STATUS = "presence";
    public const ABSENCE_STATUS = "absence";
    public const LATE_STATUS = "late";
    public const AUTHORIZED_STATUS = "authorized";

    public static function status(){
        return [
            self::PRESENCE_STATUS => 'حضور',
            self::ABSENCE_STATUS => 'غياب',
            self::LATE_STATUS => 'متأخر',
            self::AUTHORIZED_STATUS => 'مأذون',
        ];
    }
}
