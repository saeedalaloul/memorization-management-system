<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\Tester;
use App\Models\Student;
use App\Models\SunnahPart;
use App\Models\ExamSuccessMark;
use App\Models\SunnahImprovementExam;
use App\Models\SunnahExternalExam;

class SunnahExam extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'mark',
        'sunnah_part_id',
        'student_id',
        'teacher_id',
        'tester_id',
        'exam_success_mark_id',
        'datetime',
        'notes',
    ];

    public const INDIVIDUAL_TYPE = "individual";
    public const DESERVED_TYPE = "deserved";

    public static function types()
    {
        return [
            self::INDIVIDUAL_TYPE => 'منفرد',
            self::DESERVED_TYPE => 'تجميعي',
        ];
    }

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('sunnahPart', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            })->OrwhereHas('student', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            })->OrwhereHas('teacher', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            });
    }

    // علاقة بين الإختبارات والمحفظين لجلب اسم المحفظ في جدول الإختبارات
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // علاقة بين الإختبارات والمختبرين لجلب اسم المختبر في جدول الإختبارات
    public function tester()
    {
        return $this->belongsTo(Tester::class, 'tester_id');
    }

    // علاقة بين الإختبارات والطلاب لجلب اسم الطالب في جدول طلبات الإختبارات
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // علاقة بين الإختبارات وجدول أجزاء القرآن لجلب اسم جزء الإختبار في جدول الإختبارات
    public function sunnahPart()
    {
        return $this->belongsTo(SunnahPart::class, 'sunnah_part_id');
    }

    // علاقة بين الإختبارات وجدول نسب النجاح في الإختبارات لجلب نسبة النجاح في الإختبار في جدول الإختبارات
    public function exam_success_mark()
    {
        return $this->belongsTo(ExamSuccessMark::class, 'exam_success_mark_id');
    }

    // علاقة بين الإختبارات وجدول تحسين الدرجة في الإختبارات لجلب درجة التحسين في جدول الإختبارات
    public function exam_improvement()
    {
        return $this->hasOne(SunnahImprovementExam::class, 'id');
    }

    // علاقة بين الإختبارات وجدول الاختبارات الخارجية في الإختبارات لجلب درجة الاختبارات الخارجية في جدول الإختبارات
    public function external_exam()
    {
        return $this->hasOne(SunnahExternalExam::class, 'id');
    }
}
