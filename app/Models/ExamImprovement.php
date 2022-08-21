<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class ExamImprovement extends Model
{
    use SimpleUuid;

    protected $table = 'exams_improvement';


    protected $fillable = [
        'id',
        'mark',
        'tester_id',
        'datetime',
    ];

    // علاقة بين جدول تحسين درجات الاختبارات والاختبارات لجلب معلومات الاختبار في جدول تحسين درجات الاختبارات
    public function exam()
    {
        return $this->belongsTo('App\Models\Exam', 'id');
    }

    // علاقة بين الإختبارات والمختبرين لجلب اسم المختبر في جدول الإختبارات
    public function tester()
    {
        return $this->belongsTo('App\Models\Tester', 'tester_id');
    }
}
