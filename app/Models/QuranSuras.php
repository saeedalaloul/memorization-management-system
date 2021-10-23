<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranSuras extends Model
{
    use HasFactory;

    protected $fillable = ['name','total_number_aya'];

    public $timestamps = false;
}
