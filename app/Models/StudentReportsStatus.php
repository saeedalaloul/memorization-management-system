<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class StudentReportsStatus extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'student_id',
        'status',
        'details',
    ];

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
           ->OrwhereHas('student', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            });
    }

    public const SEND_FAILURE_STATUS = "send_failure";
    public const READY_TO_SEND_STATUS = "ready_to_send";

    public static function status(){
        return [
            self::SEND_FAILURE_STATUS => 'فشل الإرسال',
            self::READY_TO_SEND_STATUS => 'جاهز للإرسال',
        ];
    }

    // علاقة بين حالة تقارير الطلاب والطلاب لجلب اسم الطالب في جدول حالة تقارير الطلاب
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
