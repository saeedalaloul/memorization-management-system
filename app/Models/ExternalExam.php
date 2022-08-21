<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class ExternalExam extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'id',
        'mark',
        'date',
    ];
    public $timestamps = false;

    // علاقة بين جدول تحسين درجات الاختبارات والاختبارات لجلب معلومات الاختبار في جدول تحسين درجات الاختبارات
    public function exam()
    {
        return $this->belongsTo('App\Models\Exam', 'id');
    }
}
