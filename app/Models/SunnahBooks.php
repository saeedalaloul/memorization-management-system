<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SunnahBooks extends Model
{
    protected $fillable = ['name', 'total_number_hadith'];

    public $timestamps = false;
}
