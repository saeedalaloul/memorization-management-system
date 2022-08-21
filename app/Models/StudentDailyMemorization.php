<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class StudentDailyMemorization extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'student_id', 'teacher_id',
        'type', 'sura_from_id', 'number_pages',
        'sura_to_id', 'aya_from', 'aya_to',
        'evaluation', 'datetime',
    ];

    const MEMORIZE_TYPE = "memorize";
    const REVIEW_TYPE = "review";
    const CUMULATIVE_REVIEW_TYPE = "cumulative-review";

    const EXCELLENT_EVALUATION = "excellent";
    const VERY_GOOD_EVALUATION = "very-good";
    const GOOD_EVALUATION = "good";
    const WEAK_EVALUATION = "weak";

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

    public function scopeAyaFrom()
    {
        return $this->quranSuraFrom->total_number_aya == $this->aya_from ? 'كاملة' : $this->aya_from;
    }

    public function scopeAyaTo()
    {
        return $this->quranSuraTo->total_number_aya == $this->aya_to ? 'كاملة' : $this->aya_to;
    }

    public function scopeTypeName()
    {
        if ($this->type == self::MEMORIZE_TYPE) {
            return 'حفظ';
        } elseif ($this->type == self::REVIEW_TYPE) {
            return 'مراجعة';
        } else {
            return 'مراجعة تجميعي';
        }
    }

    public function scopeEvaluation()
    {
        if ($this->evaluation == 'excellent') {
            return 'ممتاز';
        } elseif ($this->evaluation == 'very-good') {
            return 'جيد جدا';
        } elseif ($this->evaluation == 'good') {
            return 'جيد';
        } else {
            return 'ضعيف';
        }
    }

    // علاقة بين الحفظ اليومي للطالب والمحفظين لجلب اسم المحفظ في جدول الحفظ اليومي للطالب
    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher', 'teacher_id');
    }

    // علاقة بين الحفظ اليومي للطالب والطلاب لجلب اسم الطالب في جدول الحفظ اليومي للطالب
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }

    // علاقة بين الحفظ اليومي للطالب وجدول سور القرآن لجلب اسم سورة القرآن في جدول الحفظ اليومي للطالب
    public function quranSuraFrom()
    {
        return $this->belongsTo('App\Models\QuranSuras', 'sura_from_id');
    }

    // علاقة بين الحفظ اليومي للطالب وجدول سور القرآن لجلب اسم سورة القرآن في جدول الحفظ اليومي للطالب
    public function quranSuraTo()
    {
        return $this->belongsTo('App\Models\QuranSuras', 'sura_to_id');
    }
}
