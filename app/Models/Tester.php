<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Tester extends Model
{
    use HasFactory;

    protected $fillable = ['id'];
    public $timestamps = false;

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('user', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            });
    }

    // علاقة بين المختبرين والمستخدمين لجلب اسم المختبر في جدول المختبرين
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    // علاقة بين المختبرين وجدول الإختبارات لجلب عدد الإختبارات في جدول المختبرين
    public function exams(){
        return $this->hasMany('App\Models\Exam','tester_id','id');
    }

    // علاقة بين المختبرين وجدول طلبات الإختبارات لجلب عدد طلبات الإختبارات في جدول المختبرين
    public function exams_orders(){
        return $this->hasMany('App\Models\ExamOrder','tester_id','id');
    }
}
