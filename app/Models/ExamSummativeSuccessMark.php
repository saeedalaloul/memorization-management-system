<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSummativeSuccessMark extends Model
{
    use HasFactory;

    protected $table = 'exam_summative_success_mark';

    protected $fillable = ['mark'];
}
