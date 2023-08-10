<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class TrackStudentTransfer extends Model
{
    use SimpleUuid;

    protected $fillable = ['id', 'student_id', 'old_grade_id', 'old_teacher_id', 'new_grade_id',
        'new_teacher_id', 'user_signature_id', 'user_signature_role_id'];

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('student', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            })->OrwhereHas('old_teacher', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            })->OrwhereHas('new_teacher', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            })->OrwhereHas('user_signature', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            });
    }

    // علاقة بين جدول تتبع عمليات نقل الطلاب والطلاب لجلب اسم الطالب في الجدول
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // علاقة بين جدول تتبع عمليات نقل الطلاب والمحفظين لجلب اسم المحفظ القديم في الجدول
    public function old_teacher()
    {
        return $this->belongsTo(Teacher::class, 'old_teacher_id','id');
    }

    // علاقة بين جدول تتبع عمليات نقل الطلاب والمحفظين لجلب اسم المحفظ الجديد في الجدول
    public function new_teacher()
    {
        return $this->belongsTo(Teacher::class, 'new_teacher_id','id');
    }

    // علاقة بين جدول تتبع عمليات نقل الطلاب والمراحل لجلب اسم المرحلة القديم في الجدول
    public function old_grade()
    {
        return $this->belongsTo(Grade::class, 'old_grade_id','id');
    }

    // علاقة بين جدول تتبع عمليات نقل الطلاب والمراحل لجلب اسم المرحلة الجديد في الجدول
    public function new_grade()
    {
        return $this->belongsTo(Grade::class, 'new_grade_id','id');
    }

    // علاقة بين جدول تتبع عمليات نقل الطلاب والمستخدم الذي أشرف عليها لجلب اسم المستخدم في الجدول
    public function user_signature()
    {
        return $this->belongsTo(User::class, 'user_signature_id');
    }

    // علاقة بين جدول تتبع عمليات نقل الطلاب ودور المستخدم لجلب اسم الدور في الجدول
    public function user_signature_role()
    {
        return $this->belongsTo(Role::class, 'user_signature_role_id');
    }
}
