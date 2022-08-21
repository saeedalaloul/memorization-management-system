<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'mark',
        'quran_part_id',
        'student_id',
        'teacher_id',
        'tester_id',
        'exam_success_mark_id',
        'datetime',
        'notes',
    ];

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
            });
    }

    // علاقة بين الإختبارات والمحفظين لجلب اسم المحفظ في جدول الإختبارات
    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher', 'teacher_id');
    }

    // علاقة بين الإختبارات والمختبرين لجلب اسم المختبر في جدول الإختبارات
    public function tester()
    {
        return $this->belongsTo('App\Models\Tester', 'tester_id');
    }

    // علاقة بين الإختبارات والطلاب لجلب اسم الطالب في جدول طلبات الإختبارات
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }

    // علاقة بين الإختبارات وجدول أجزاء القرآن لجلب اسم جزء الإختبار في جدول الإختبارات
    public function quranPart()
    {
        return $this->belongsTo('App\Models\QuranPart', 'quran_part_id');
    }

    // علاقة بين الإختبارات وجدول نسب النجاح في الإختبارات لجلب نسبة النجاح في الإختبار في جدول الإختبارات
    public function examSuccessMark()
    {
        return $this->belongsTo('App\Models\ExamSuccessMark', 'exam_success_mark_id');
    }

    // علاقة بين الإختبارات وجدول تحسين الدرجة في الإختبارات لجلب درجة التحسين في جدول الإختبارات
    public function exam_improvement()
    {
        return $this->hasOne('App\Models\ExamImprovement', 'id');
    }

    // علاقة بين الإختبارات وجدول الاختبارات الخارجية في الإختبارات لجلب درجة الاختبارات الخارجية في جدول الإختبارات
    public function external_exam()
    {
        return $this->hasOne('App\Models\ExternalExam', 'id');
    }
}
