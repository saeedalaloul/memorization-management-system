<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranSuras extends Model
{
    use HasFactory;

    protected $fillable = ['name','quran_part_id','total_number_aya'];

    public $timestamps = false;

    // علاقة بين سور القرآن وجدول أجزاء القرآن لجلب اسم جزء الإختبار في جدول سور القرآن
    public function quranPart()
    {
        return $this->belongsTo('App\Models\QuranPart', 'quran_part_id');
    }
}
