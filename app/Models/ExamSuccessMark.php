<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSuccessMark extends Model
{
    use HasFactory;

    protected $table = 'exam_success_mark';

    protected $fillable = ['mark'];

    public $timestamps = false;
}
