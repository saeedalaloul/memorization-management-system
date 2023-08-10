<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;
use App\Models\SunnahExam;
use App\Models\Tester;

class SunnahImprovementExam extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'id',
        'mark',
        'tester_id',
        'datetime',
    ];

    // علاقة بين جدول تحسين درجات الاختبارات والاختبارات لجلب معلومات الاختبار في جدول تحسين درجات الاختبارات
    public function exam()
    {
        return $this->belongsTo(SunnahExam::class, 'id');
    }

    // علاقة بين الإختبارات والمختبرين لجلب اسم المختبر في جدول الإختبارات
    public function tester()
    {
        return $this->belongsTo(Tester::class, 'tester_id');
    }
}
