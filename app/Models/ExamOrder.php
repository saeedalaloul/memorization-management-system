<?php

namespace App\Models;


use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Tester;

class ExamOrder extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'status',
        'type',
        'partable_id',
        'partable_type',
        'student_id',
        'teacher_id',
        'tester_id',
        'user_signature_id',
        'datetime',
        'suggested_day',
        'notes',
    ];

    public const IN_PENDING_STATUS = "in-pending";
    public const REJECTED_STATUS = "rejected";
    public const ACCEPTABLE_STATUS = "acceptable";
    public const FAILURE_STATUS = "failure";

    public const NEW_TYPE = "new";
    public const IMPROVEMENT_TYPE = "improvement";

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
            ->OrwhereHasMorph(
                'partable',
                [QuranPart::class, SunnahPart::class],
                function (Builder $query) use ($val) {
                    $query->where('name', 'LIKE', "%$val%");
                }
            )->OrwhereHas('student', function ($q) use ($val) {
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
            ->where('status', self::ACCEPTABLE_STATUS);
    }

    public function partable()
    {
        return $this->morphTo();
    }

    // علاقة بين طلبات الإختبارات والطلاب لجلب اسم الطالب في جدول طلبات الإختبارات
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // علاقة بين طلبات الإختبارات والمحفظين لجلب اسم المحفظ في جدول طلبات الإختبارات
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // علاقة بين طلبات الإختبارات والمختبرين لجلب اسم المختبر في جدول طلبات الإختبارات
    public function tester()
    {
        return $this->belongsTo(Tester::class, 'tester_id');
    }

    // علاقة بين طلبات الإختبارات وتوقيع المستخدم لجلب اسم صاحب التوقيع في جدول طلبات الإختبارات
    public function user_signature()
    {
        return $this->belongsTo('App\Models\User', 'user_signature_id');
    }
}
