<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreventStatusStudent extends Model
{
    protected $fillable = [
        'student_id',
    ];
    protected $primaryKey = 'student_id';

    // علاقة بين جدول حالة منع الطالب والطلاب لجلب اسم الطالب في جدول حالة منع الطالب
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }
}
