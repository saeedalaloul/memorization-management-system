<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDailyPreservation extends Model
{

    protected $fillable = [
        'student_id', 'teacher_id',
        'type', 'from_sura',
        'to_sura', 'from_aya', 'to_aya',
        'evaluation', 'daily_preservation_date',
    ];

    public $timestamps = false;

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

    public function scopeFromAya()
    {
        return $this->quranSuraFrom->total_number_aya == $this->from_aya ? 'كاملة' : $this->from_aya;
    }

    public function scopeToAya()
    {
        return $this->quranSuraTo->total_number_aya == $this->to_aya ? 'كاملة' : $this->to_aya;
    }

    public function scopeCalcNumberPages()
    {
        if ($this->from_sura == $this->to_sura) {
            return round((AyaDetails::query()
                    ->where('sura_name', '=', $this->quranSuraFrom->name)
                    ->whereBetween('aya_number', [$this->from_aya, $this->to_aya])
                    ->sum('aya_percent')) / 15, 2);
        } else {
            // جلب أول سورة لحساب عدد الصفحات.
            $sura_start = (AyaDetails::query()
                    ->where('sura_name', '=', $this->quranSuraFrom->name)
                    ->whereBetween('aya_number', [$this->from_aya, $this->quranSuraFrom->total_number_aya])
                    ->sum('aya_percent')) / 15;
            // جلب السور ما بين أول سور وأخر سورة لحساب عدد الصفحات.
            $suras_between = (AyaDetails::query()
                    ->whereBetween('sura_name', [$this->quranSuraTo->name,$this->quranSuraFrom->name])
                    ->sum('aya_percent')) / 15;
            // جلب أخر سورة لحساب عدد الصفحات.
            $sura_end = (AyaDetails::query()
                    ->where('sura_name', '=', $this->quranSuraTo->name)
                    ->whereBetween('aya_number', [1, $this->to_aya])
                    ->sum('aya_percent')) / 15;

            return round($sura_start + $suras_between + $sura_end, 2);
        }
    }

    // علاقة بين الحفظ اليومي للطالب والمحفظين لجلب اسم المحفظ في جدول الحفظ اليومي للطالب
    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher', 'teacher_id');
    }

    // علاقة بين الحفظ اليومي للطالب والطلاب لجلب اسم الطالب في جدول الحفظ اليومي للطالب
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }

    // علاقة بين الحفظ اليومي للطالب وجدول سور القرآن لجلب اسم سورة القرآن في جدول الحفظ اليومي للطالب
    public function quranSuraFrom()
    {
        return $this->belongsTo('App\Models\QuranSuras', 'from_sura',);
    }

    // علاقة بين الحفظ اليومي للطالب وجدول سور القرآن لجلب اسم سورة القرآن في جدول الحفظ اليومي للطالب
    public function quranSuraTo()
    {
        return $this->belongsTo('App\Models\QuranSuras', 'to_sura');
    }

    // علاقة بين الحفظ اليومي للطالب وجدول تقييم الحفظ اليومي لجلب تقييم الحفظ اليومي في جدول الحفظ اليومي للطالب
    public function dailyPreservationEvaluation()
    {
        return $this->belongsTo('App\Models\DailyPreservationEvaluation', 'evaluation');
    }

    // علاقة بين الحفظ اليومي للطالب وجدول نوع الحفظ اليومي لجلب نوع الحفظ اليومي في جدول الحفظ اليومي للطالب
    public function dailyPreservationType()
    {
        return $this->belongsTo('App\Models\DailyPreservationType', 'type');
    }
}
