<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use App\Models\Father;
use App\Models\Grade;
use App\Models\StudentDailyMemorization;
use App\Models\StudentAttendance;
use App\Models\StudentSunnahAttendance;
use App\Models\ExamOrder;
use App\Models\Exam;
use App\Models\StudentWarning;
use App\Models\StudentBlock;

class Student extends Model
{
    protected $fillable = ['id', 'father_id', 'grade_id', 'group_id','group_sunnah_id',
        'whatsapp_number','current_revision_count','current_part_id',
        'current_part_cumulative_id','current_cumulative_revision_count'];

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->where('whatsapp_number', 'like', '%' . $val . '%')
            ->OrwhereHas('user', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            });
    }

    // علاقة بين الطلاب والمستخدمين لجلب اسم الطالب في جدول الطلاب
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    // علاقة بين الطلاب والأباء لجلب اسم الأب في جدول الطلاب
    public function father()
    {
        return $this->belongsTo(Father::class, 'father_id');
    }

    // علاقة بين الطلاب والمراحل لجلب اسم المرحلة في جدول الطلاب
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    // علاقة بين الطلاب والحلقات لجلب اسم الحلقة في جدول الطلاب
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    // علاقة بين الطلاب وحلقات السنة لجلب اسم الحلقة في جدول الطلاب (إن وجد).
    public function group_sunnah()
    {
        return $this->belongsTo(Group::class, 'group_sunnah_id');
    }

    public function exam_orders()
    {
        return $this->hasMany(ExamOrder::class, 'student_id');
    }

    // علاقة بين جدول الطلاب وجدول الحضور والغياب
    public function daily_memorization()
    {
        return $this->hasMany(StudentDailyMemorization::class, 'student_id');
    }

    // علاقة بين جدول الطلاب وجدول الحضور والغياب
    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id');
    }

    // علاقة بين جدول الطلاب وجدول الحضور والغياب اليوم
    public function attendance_today()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id')->whereDate('datetime', date('Y-m-d'));
    }

    // علاقة بين جدول الطلاب وجدول الحضور والغياب لحلقة السنة
    public function attendance_sunnah()
    {
        return $this->hasMany(StudentSunnahAttendance::class, 'student_id');
    }

    // علاقة بين جدول الطلاب وجدول الحضور والغياب اليوم لحلقة السنة
    public function attendance_sunnah_today()
    {
        return $this->hasMany(StudentSunnahAttendance::class, 'student_id')->whereDate('datetime', date('Y-m-d'));
    }

    // علاقة بين جدول الطلاب وجدول طلبات الإختبارات
    public function exam_order()
    {
        return $this->hasMany(ExamOrder::class, 'student_id');
    }

    // علاقة بين جدول الطلاب وجدول الإختبارات القرآنية
    public function exams()
    {
        return $this->hasMany(Exam::class, 'student_id');
    }

    // علاقة بين الإنذارات التي لم يتم إلغائها والطلاب لعرض الإنذار في جدول الطلاب
    public function student_is_warning()
    {
        return $this->hasOne(StudentWarning::class, 'student_id', 'id')->whereNull('warning_expiry_date');
    }

    // علاقة بين الإنذارات والطلاب لعرض الإنذار في جدول الطلاب
    public function student_warning()
    {
        return $this->hasOne(StudentWarning::class, 'student_id', 'id');
    }

    // علاقة بين الحظر الذي لم يتم إلغائه والطلاب لعرض الحظر في جدول الطلاب
    public function student_is_block()
    {
        return $this->hasOne(StudentBlock::class, 'student_id', 'id')->whereNull('block_expiry_date');
    }

    // علاقة بين الحظر والطلاب لعرض الحظر في جدول الطلاب
    public function student_block()
    {
        return $this->hasOne(StudentBlock::class, 'student_id', 'id');
    }

    // علاقة بين الطلاب وجدول أجزاء القرآن لجلب اسم جزء الإختبار الحالي في جدول الطلاب
    public function current_Part()
    {
        return $this->belongsTo(QuranPart::class, 'current_part_id');
    }
}
