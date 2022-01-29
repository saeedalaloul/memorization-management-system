<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'allow_exams_update',
        'exam_questions_min', 'exam_questions_max',
        'number_days_exam', 'exam_success_rate',
        'exam_questions_summative_three_part', 'exam_questions_summative_five_part',
        'exam_questions_summative_ten_part', 'exam_questions_summative_fifteen_part',
    ];

    public $timestamps = false;
}
