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

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->orWhereHas('exam', function ($q) use ($val) {
                $q->whereHas('quranPart', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                })->orWhereHas('student', function ($q) use ($val) {
                    $q->whereHas('user', function ($q) use ($val) {
                        $q->where('name', 'LIKE', "%$val%");
                    });
                })->orWhereHas('teacher', function ($q) use ($val) {
                    $q->whereHas('user', function ($q) use ($val) {
                        $q->where('name', 'LIKE', "%$val%");
                    });
                });
            });
    }

    // علاقة بين جدول تحسين درجات الاختبارات والاختبارات لجلب معلومات الاختبار في جدول تحسين درجات الاختبارات
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'id');
    }
}
