<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentWarning extends Model
{

    protected $fillable = [
        'student_id',
        'warning_expiry_date',
        'notes',
        'readable',
    ];

    protected $casts = [
        'readable' => 'array',
    ];

    // علاقة بين الإنذارات والطلاب لجلب اسم الطالب في جدول طلبات الإنذارات
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }

    public function scopeUnreadWarnings()
    {
        if (auth()->user()->current_role == 'محفظ') {
            return $this
                ->whereHas('student', function ($q) {
                    return $q->where('group_id', '=', Teacher::where('id', auth()->id())->first()->group->id);
                })
                ->where('readable->isReadableTeacher', false)
                ->count();
        } else if (auth()->user()->current_role == 'مشرف') {
           return $this
                ->where('readable->isReadableSupervisor', false)
                ->whereHas('student', function ($q) {
                    return $q->where('grade_id', '=', Supervisor::where('id', auth()->id())->first()->grade_id);
                })
                ->count();
        }
        return 0;
    }

    public function scopeWarnings()
    {
        if (auth()->user()->current_role == 'محفظ') {
            return $this
                ->whereHas('student', function ($q) {
                    return $q->where('group_id', '=', Teacher::where('id', auth()->id())->first()->group->id);
                })
                ->where('readable->isReadableTeacher', false)
                ->get();
        } else if (auth()->user()->current_role == 'مشرف') {
            return $this
                ->where('readable->isReadableSupervisor', false)
                ->whereHas('student', function ($q) {
                    return $q->where('grade_id', '=', Supervisor::where('id', auth()->id())->first()->grade_id);
                })
                ->get();
        }
        return 0;
    }
}