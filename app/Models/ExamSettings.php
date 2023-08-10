<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSettings extends Model
{
    protected $fillable = [
        'suggested_exam_days','exam_questions_min', 'exam_questions_max'
        ,'exam_sunnah_questions_summative','exam_sunnah_questions','number_days_exam_sunnah'
        , 'number_days_exam','number_days_exam_two_left','number_days_exam_three_left', 'exam_success_rate',
        'summative_exam_success_rate','exam_questions_summative_three_part', 'exam_questions_summative_five_part'
        , 'exam_questions_summative_ten_part','exam_sunnah_success_rate'
    ];


    const SATURDAY_DAY = "saturday";
    const SUN_DAY = "sunday";
    const MON_DAY = "monday";
    const TUES_DAY = "tuesday";
    const WEDNES_DAY = "wednesday";
    const THURS_DAY = "thursday";
    const FRI_DAY = "friday";

    public static function days()
    {
        return [
            self::SATURDAY_DAY => 'السبت',
            self::SUN_DAY => 'الأحد',
            self::MON_DAY => 'الإثنين',
            self::TUES_DAY => 'الثلاثاء',
            self::WEDNES_DAY => 'الأربعاء',
            self::THURS_DAY => 'الخميس',
            self::FRI_DAY => 'الجمعة',
        ];
    }

}
