<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'grade_id'];
    public $timestamps = false;

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('user', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            });
    }

    // علاقة بين المحفظين والمستخدمين لجلب اسم المحفظ في جدول المحفظين
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    // علاقة بين المحفظين والمراحل لجلب اسم المرحلة في جدول المحفظين
    public function grade()
    {
        return $this->belongsTo('App\Models\Grade', 'grade_id');
    }

    // علاقة بين المحفظين والمجموعات لجلب اسم المجموعة في جدول المحفظين
    public function group()
    {
        return $this->hasOne('App\Models\Group');
    }

    // علاقة بين جدول المحفظين وجدول الحضور والغياب
    public function attendance()
    {
        return $this->hasMany('App\Models\TeacherAttendance', 'teacher_id');
    }

    // علاقة بين جدول المحفظين وجدول الحضور والغياب التابع للطلاب
    public function attendance_student()
    {
        return $this->hasMany('App\Models\StudentAttendance', 'teacher_id');
    }

    // علاقة بين جدول المحفظين وجدول الحضور والغياب التابع للطلاب
    public function student_daily_preservation()
    {
        return $this->hasMany('App\Models\StudentDailyPreservation', 'teacher_id');
    }

    // علاقة بين جدول المحفظين وجدول طلبات الإختبارات
    public function exam_order()
    {
        return $this->hasMany('App\Models\ExamOrder', 'teacher_id');
    }

    // علاقة بين جدول المحفظين وجدول الإختبارات القرآنية
    public function exam()
    {
        return $this->hasMany('App\Models\Exam', 'teacher_id');
    }
}
