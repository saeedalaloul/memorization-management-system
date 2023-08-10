<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranPart extends Model
{
    protected $fillable = ['name', 'arrangement', 'total_preservation_parts', 'type', 'description'];

    public $timestamps = false;

    public const QURAN_MEMORIZER_PART = 1;
    public const INDIVIDUAL_TYPE = "individual";
    public const DESERVED_TYPE = "deserved";

    public static function types()
    {
        return [
            self::INDIVIDUAL_TYPE => 'منفرد',
            self::DESERVED_TYPE => 'تجميعي',
        ];
    }

    public function examCustomQuestion()
    {
        return $this->hasMany(ExamCustomQuestion::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    // علاقة بين أجزاء القرآن وطلبات الاختبارات لجلب طلبات الاختبارات في جدول أجزاء القرآن

    public function exam_orders()
    {
        return $this->morphMany(ExamOrder::class, 'partable');
    }
}
