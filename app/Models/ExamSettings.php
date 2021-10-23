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
        'number_days_exam', 'exam_success_rate'
    ];

    public $timestamps = false;
}
