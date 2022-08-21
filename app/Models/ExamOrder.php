<?php

namespace App\Models;


use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class ExamOrder extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'status',
        'type',
        'quran_part_id',
        'student_id',
        'teacher_id',
        'tester_id',
        'user_signature_id',
        'datetime',
        'notes',
    ];

    const IN_PENDING_STATUS = "in-pending";
    const REJECTED_STATUS = "rejected";
    const ACCEPTABLE_STATUS = "acceptable";
    const FAILURE_STATUS = "failure";

    const NEW_TYPE = "new";
    const IMPROVEMENT_TYPE = "improvement";

    public static function status()
    {
        return [
            self::IN_PENDING_STATUS => 'قيد الطلب',
            self::REJECTED_STATUS => 'مرفوض',
            self::ACCEPTABLE_STATUS => 'معتمد',
            self::FAILURE_STATUS => 'لم يختبر',
        ];
    }

    public static function types()
    {
        return [
            self::NEW_TYPE => 'طلب جديد',
            self::IMPROVEMENT_TYPE => 'طلب تحسين درجة',
        ];
    }


    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('quranPart', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            })->OrwhereHas('student', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            })->OrwhereHas('teacher', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            })->OrwhereHas('tester', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            });
    }

    public function scopeTodayExams($query)
    {
        return $query
            ->whereDate('datetime', date('Y-m-d', time()))
            ->where('status', ExamOrder::ACCEPTABLE_STATUS);
    }

    // علاقة بين طلبات الإختبارات والمحفظين لجلب اسم المحفظ في جدول طلبات الإختبارات
    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher', 'teacher_id');
    }

    // علاقة بين طلبات الإختبارات والطلاب لجلب اسم الطالب في جدول طلبات الإختبارات
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }

    // علاقة بين طلبات الإختبارات والمختبرين لجلب اسم المختبر في جدول طلبات الإختبارات
    public function tester()
    {
        return $this->belongsTo('App\Models\Tester', 'tester_id');
    }

    // علاقة بين طلبات الإختبارات وتوقيع المستخدم لجلب اسم صاحب التوقيع في جدول طلبات الإختبارات
    public function user_signature()
    {
        return $this->belongsTo('App\Models\User', 'user_signature_id');
    }

    // علاقة بين طلبات الإختبارات وجدول أجزاء القرآن لجلب اسم جزء الإختبار في جدول طلبات الإختبارات
    public function quranPart()
    {
        return $this->belongsTo('App\Models\QuranPart', 'quran_part_id');
    }

}
