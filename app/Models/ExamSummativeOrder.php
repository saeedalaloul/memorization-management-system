<?php

namespace App\Models;


use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSummativeOrder extends Model
{
    use HasFactory,SimpleUuid;

    protected $fillable = [
        'status',
        'quran_summative_part_id',
        'student_id',
        'teacher_id',
        'tester_id',
        'exam_date',
        'readable',
        'notes',
    ];

    protected $casts = [
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
            })->OrwhereHas('tester', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            });
    }

    public function scopeTodayExams($query)
    {
        return $query
            ->whereDate('exam_date', date('Y-m-d', time()))
            ->where('status', 2);
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
        } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
            return $this
                ->where('readable->isReadableSupervisorExams', false)
                ->whereIn('status', [1, 2, -2, -3])
                ->count();
        } else if (auth()->user()->current_role == 'مختبر') {
            return $this
                ->where('readable->isReadableTester', false)
                ->where('tester_id', auth()->id())
                ->whereIn('status', [2, -3])
                ->count();
        }
        return 0;
    }

    public function scopeUnreadTodayExams()
    {
        if (auth()->user()->current_role == 'محفظ') {
            return $this
                ->where('teacher_id', auth()->id())
                ->whereDate('exam_date', date('Y-m-d', time()))
                ->where('status', 2)
                ->where('readable->isReadableTeacher', false)
                ->count();
        } else if (auth()->user()->current_role == 'مشرف') {
            return $this
                ->where('readable->isReadableSupervisor', false)
                ->whereDate('exam_date', date('Y-m-d', time()))
                ->where('status', 2)
                ->whereHas('student', function ($q) {
                    return $q->where('grade_id', '=', Supervisor::where('id', auth()->id())->first()->grade_id);
                })
                ->count();
        } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
            return $this
                ->where('readable->isReadableSupervisorExams', false)
                ->whereDate('exam_date', date('Y-m-d', time()))
                ->where('status', 2)
                ->count();
        } else if (auth()->user()->current_role == 'مختبر') {
            return $this
                ->where('readable->isReadableTester', false)
                ->whereDate('exam_date', date('Y-m-d', time()))
                ->where('status', 2)
                ->where('tester_id', auth()->id())
                ->count();
        }
        return 0;
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

    // علاقة بين طلبات الإختبارات وجدول أجزاء القرآن لجلب اسم جزء الإختبار في جدول طلبات الإختبارات
    public function quranPart()
    {
        return $this->belongsTo('App\Models\QuranSummativePart', 'quran_summative_part_id');
    }

}
