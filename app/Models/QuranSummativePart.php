<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranSummativePart extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'number_parts', 'description'];

    public $timestamps = false;

    public function scopeQuranSummativePartName()
    {
        return $this->name . ' (' . $this
                ->description . ') ' . $this
                ->number_parts;
    }
}
