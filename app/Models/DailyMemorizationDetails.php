<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyMemorizationDetails extends Model
{
    protected $fillable = [
        'id', 'sura_id','aya_from','aya_to'
    ];

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('student', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            });
    }

    public function scopeAyaFrom()
    {
        return $this->quranSuraFrom->total_number_aya == $this->aya_from ? 'كاملة' : $this->aya_from;
    }

    public function scopeAyaTo()
    {
        return $this->quranSuraTo->total_number_aya == $this->aya_to ? 'كاملة' : $this->aya_to;
    }

    // علاقة بين تفاصيل الحفظ اليومي للطالب وجدول الحفظ اليومي
//    public function daily_memorization()
//    {
//        return $this->hasOne(StudentDailyMemorization::class, 'id');
//    }

    // علاقة بين تفاصيل الحفظ اليومي للطالب وجدول سور القرآن لجلب اسم سورة القرآن في جدول الحفظ اليومي للطالب
    public function quranSura()
    {
        return $this->belongsTo(QuranSuras::class, 'sura_id');
    }
}
