<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSettings extends Model
{
    protected $fillable = [
         'exam_questions_min', 'exam_questions_max', 'number_days_exam', 'exam_success_rate','summative_exam_success_rate',
        'exam_questions_summative_three_part', 'exam_questions_summative_five_part', 'exam_questions_summative_ten_part',
    ];

}
