<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['id', 'grade_id'];

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('user', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%")
                    ->OrWhere('identification_number', 'LIKE', "%$val%")
                    ->OrWhere('phone', 'LIKE', "%$val%");
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

    // علاقة بين جدول المحفظين وجدول الحضور والغياب
    public function attendance_today()
    {
        return $this->hasMany('App\Models\TeacherAttendance', 'teacher_id')
            ->whereDate('datetime', date('Y-m-d'));
    }

    // علاقة بين جدول المحفظين وجدول الحضور والغياب التابع للطلاب
    public function attendance_student()
    {
        return $this->hasMany('App\Models\StudentAttendance', 'teacher_id');
    }

    // علاقة بين جدول المحفظين وجدول الحضور والغياب التابع للطلاب
    public function student_daily_memorization()
    {
        return $this->hasMany('App\Models\StudentDailyMemorization', 'teacher_id');
    }

    // علاقة بين جدول المحفظين وجدول طلبات الإختبارات
    public function exam_order()
    {
        return $this->hasMany('App\Models\ExamOrder', 'teacher_id','id');
    }

    // علاقة بين جدول المحفظين وجدول الإختبارات القرآنية
    public function exam()
    {
        return $this->hasMany('App\Models\Exam', 'teacher_id');
    }

    // علاقة بين الحلقات والزيارات لجلب طلبات الزيارات في جدول الحلقات

    public function visit_orders()
    {
        return $this->morphMany(VisitOrder::class, 'hostable');
    }
}
