<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranPart extends Model
{
    protected $fillable = ['name', 'arrangement', 'total_preservation_parts', 'type', 'description'];

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

    public function examCustomQuestion()
    {
        return $this->hasMany('App\Models\ExamCustomQuestion');
    }

    public function exams()
    {
        return $this->hasMany('App\Models\Exam');
    }
}
