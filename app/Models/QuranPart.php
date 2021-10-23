<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranPart extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public $timestamps = false;

    public function examCustomQuestion(){
        return $this->hasMany('App\Models\ExamCustomQuestion');
    }
}
