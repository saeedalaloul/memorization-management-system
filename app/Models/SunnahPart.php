<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SunnahPart extends Model
{
    protected $fillable = ['name', 'arrangement','sunnah_book_id','total_hadith_parts', 'type'];

    public $timestamps = false;

    const INDIVIDUAL_TYPE = "individual";
    const DESERVED_TYPE = "deserved";

    public static function types()
    {
        return [
            self::INDIVIDUAL_TYPE => 'منفرد',
            self::DESERVED_TYPE => 'تجميعي',
        ];
    }

    public function exams()
    {
        return $this->hasMany('App\Models\SunnahExam');
    }

    // علاقة بين أجزاء السنة وطلبات الاختبارات لجلب طلبات الاختبارات في جدول أجزاء السنة

    public function exam_orders()
    {
        return $this->morphMany(ExamOrder::class, 'partable');
    }

    // علاقة بين أجزاء السنة وجدول كتب السنة لجلب اسم الكتاب في جدول أجزاء السنة
    public function book()
    {
        return $this->belongsTo('App\Models\SunnahBooks', 'sunnah_book_id');
    }
}
