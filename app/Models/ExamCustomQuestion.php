<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuranPart;

class ExamCustomQuestion extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'quran_part_id', 'question_count'
    ];


    // علاقة بين جدول أسئلة الإختبارات المخصصة وجدول أجزاء القرآن لجلب اسم جزء الإختبار في جدول أسئلة الإختبارات المخصصة
    public function quranPart()
    {
        return $this->belongsTo(QuranPart::class, 'quran_part_id');
    }
}
