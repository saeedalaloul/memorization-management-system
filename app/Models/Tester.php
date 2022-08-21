<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tester extends Model
{
    protected $fillable = ['id'];
    const CACHE_KEY = "testers";

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
    public function exams()
    {
        return $this->hasMany('App\Models\Exam', 'tester_id', 'id');
    }

    // علاقة بين المختبرين وجدول طلبات الإختبارات لجلب عدد طلبات الإختبارات في جدول المختبرين
    public function exams_orders()
    {
        return $this->hasMany('App\Models\ExamOrder', 'tester_id', 'id');
    }


    // علاقة بين المختبرين وجدول طلبات الإختبارات لجلب عدد طلبات الإختبارات في جدول المختبرين
    public function tester_exams()
    {
        return $this->hasMany('App\Models\ExamOrder', 'tester_id', 'id')
            ->where('status', '=', ExamOrder::ACCEPTABLE_STATUS);
    }

    // علاقة بين المختبرين والزيارات لجلب طلبات الزيارات في جدول المختبرين

    public function visit_orders()
    {
        return $this->morphMany(VisitOrder::class, 'hostable');
    }
}
