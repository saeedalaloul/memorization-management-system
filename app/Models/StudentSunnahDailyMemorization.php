<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\SunnahBooks;

class StudentSunnahDailyMemorization extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'student_id', 'teacher_id',
        'type', 'book_id', 'hadith_from', 'hadith_to',
        'evaluation', 'datetime',
    ];

    public const MEMORIZE_TYPE = "memorize";
    public const REVIEW_TYPE = "review";

    public const EXCELLENT_EVALUATION = "excellent";
    public const VERY_GOOD_EVALUATION = "very-good";
    public const GOOD_EVALUATION = "good";
    public const WEAK_EVALUATION = "weak";

    protected $table = 'students_sunnah_daily_memorization';

    public static function types()
    {
        return [
            self::MEMORIZE_TYPE => 'حفظ',
            self::REVIEW_TYPE => 'مراجعة',
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

        return 'مراجعة';
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

    // علاقة بين الحفظ اليومي للطالب وجدول سور القرآن لجلب اسم سورة القرآن في جدول الحفظ اليومي للطالب
    public function book()
    {
        return $this->belongsTo(SunnahBooks::class, 'book_id');
    }
}
