<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\QuranSuras;

class StudentDailyMemorization extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'student_id', 'teacher_id',
        'type', 'number_pages','revision_count','cumulative_type',
        'evaluation', 'datetime',
    ];

    const MEMORIZE_TYPE = "memorize";
    const REVIEW_TYPE = "review";
    const CUMULATIVE_REVIEW_TYPE = "cumulative-review";

    const EXCELLENT_EVALUATION = "excellent";
    const VERY_GOOD_EVALUATION = "very-good";
    const GOOD_EVALUATION = "good";
    const WEAK_EVALUATION = "weak";
    const _1_CUMULATIVE_TYPE = 1;
    const _3_CUMULATIVE_TYPE = 3;
    const _5_CUMULATIVE_TYPE = 5;
    const _10_CUMULATIVE_TYPE = 10;
    const _15_CUMULATIVE_TYPE = 15;
    const _20_CUMULATIVE_TYPE = 20;
    const _25_CUMULATIVE_TYPE = 25;
    const _30_CUMULATIVE_TYPE = 30;

    protected $table = 'students_daily_memorization';

    public static function types()
    {
        return [
            self::MEMORIZE_TYPE => 'حفظ',
            self::REVIEW_TYPE => 'مراجعة',
            self::CUMULATIVE_REVIEW_TYPE => 'مراجعة تجميعي',
        ];
    }

    public static function evaluations()
    {
        return [
            self::EXCELLENT_EVALUATION => 'ممتاز',
            self::VERY_GOOD_EVALUATION => 'جيد جدا',
            self::GOOD_EVALUATION => 'جيد',
            self::WEAK_EVALUATION => 'ضعيف',
        ];
    }

    public static function CUMULATIVE_TYPES()
    {
        return [
            self::_1_CUMULATIVE_TYPE => 1,
            self::_3_CUMULATIVE_TYPE => 3,
            self::_5_CUMULATIVE_TYPE => 5,
            self::_10_CUMULATIVE_TYPE => 10,
            self::_15_CUMULATIVE_TYPE => 15,
            self::_20_CUMULATIVE_TYPE => 20,
            self::_25_CUMULATIVE_TYPE => 25,
            self::_30_CUMULATIVE_TYPE => 30,
        ];
    }

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

    public function scopeTypeName()
    {
        if ($this->type === self::MEMORIZE_TYPE) {
            return 'حفظ';
        }

        if ($this->type === self::REVIEW_TYPE) {
            return 'مراجعة';
        }

        return 'مراجعة تجميعي';
    }

    public function scopeEvaluation()
    {
        if ($this->evaluation === 'excellent') {
            return 'ممتاز';
        }

        if ($this->evaluation === 'very-good') {
            return 'جيد جدا';
        }

        if ($this->evaluation === 'good') {
            return 'جيد';
        }

        return 'ضعيف';
    }

    // علاقة بين الحفظ اليومي للطالب والمحفظين لجلب اسم المحفظ في جدول الحفظ اليومي للطالب
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // علاقة بين الحفظ اليومي للطالب والطلاب لجلب اسم الطالب في جدول الحفظ اليومي للطالب
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function daily_memorization_details()
    {
        return $this->hasMany(DailyMemorizationDetails::class,'id');
    }
}
