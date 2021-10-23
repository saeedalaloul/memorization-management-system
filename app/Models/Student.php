<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'father_id', 'grade_id', 'group_id'];
    public $timestamps = false;

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
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
        return $this->belongsTo('App\Models\Father', 'father_id');
    }

    // علاقة بين الطلاب والمراحل لجلب اسم المرحلة في جدول الطلاب
    public function grade()
    {
        return $this->belongsTo('App\Models\Grade', 'grade_id');
    }

    // علاقة بين الطلاب والحلقات لجلب اسم الحلقة في جدول الطلاب
    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id');
    }

    // علاقة بين جدول الطلاب وجدول الحضور والغياب
    public function attendance()
    {
        return $this->hasMany('App\Models\StudentAttendance', 'student_id');
    }
}
