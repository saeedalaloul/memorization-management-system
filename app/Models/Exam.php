<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory,SimpleUuid;

    protected $fillable = [
        'readable',
        'signs_questions',
        'marks_questions',
        'another_mark',
        'quran_part_id',
        'student_id',
        'teacher_id',
        'tester_id',
        'exam_success_mark_id',
        'exam_date',
        'notes',
    ];

    public $timestamps = false;

    protected $casts = [
        'signs_questions' => 'array',
        'marks_questions' => 'array',
        'readable' => 'array',
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

    public function scopeUnreadExams()
    {
        if (auth()->user()->current_role == 'محفظ') {
            return $this
                ->where('teacher_id', auth()->id())
                ->where('readable->isReadableTeacher', false)
                ->count();
        } else if (auth()->user()->current_role == 'مشرف') {
            return $this
                ->where('readable->isReadableSupervisor', false)
                ->whereHas('student', function ($q) {
                    return $q->where('grade_id', '=', Supervisor::where('id', auth()->id())->first()->grade_id);
                })
                ->count();
        } else if (auth()->user()->current_role == 'اداري') {
            return $this
                ->where('readable->isReadableLowerSupervisor', false)
                ->whereHas('student', function ($q) {
                    return $q->where('grade_id', '=', LowerSupervisor::where('id', auth()->id())->first()->grade_id);
                })
                ->count();
        } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
            return $this
                ->where('readable->isReadableSupervisorExams', false)
                ->count();
        } else if (auth()->user()->current_role == 'مختبر') {
            return $this
                ->where('readable->isReadableTester', false)
                ->where('tester_id', auth()->id())
                ->count();
        }
        return 0;
    }

    public function scopeCalcMarkExam()
    {
        $sum = 0;
        for ($i = 1; $i <= count($this->marks_questions); $i++) {
            $sum += $this->marks_questions[$i];
        }
        return round(100 - $sum) - (10 - $this->another_mark);
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
}
