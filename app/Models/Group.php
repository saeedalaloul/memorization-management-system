<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use SimpleUuid;

    protected $fillable = ['name', 'grade_id','type','teacher_id'];

    const QURAN_TYPE = "quran";
    const SUNNAH_TYPE = "sunnah";
    const MONTADA_TYPE = "montada";

    public static function types()
    {
        return [
            self::QURAN_TYPE => 'قرآن',
            self::SUNNAH_TYPE => 'سنة',
            self::MONTADA_TYPE => 'منتدى حفاظ',
        ];
    }

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->Orwhere('name', 'like', '%' . $val . '%')
            ->OrwhereHas('teacher', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'like', "%$val%");
                });
            });
    }

    // علاقة بين الحلقات والمراحل لجلب اسم المرحلة في جدول الحلقات

    public function grade()
    {
        return $this->belongsTo('App\Models\Grade', 'grade_id');
    }

    // علاقة بين الحلقات والمحفظين لجلب اسم المحفظ في جدول الحلقات

    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher', 'teacher_id');
    }

    // علاقة بين الحلقات والطلاب لجلب اسم الطالب في جدول الحلقات

    public function students()
    {
        return $this->hasMany(Student::class, 'group_id');
    }

    public function students_sunnah()
    {
        return $this->hasMany(Student::class, 'group_sunnah_id');
    }

    // علاقة بين الحلقات والمحفظين لجلب اسم المحفظ في جدول الحلقات

    public function punitive_measures()
    {
        return $this->belongsToMany('App\Models\PunitiveMeasure', 'punitive_measure_groups');
    }

    public function sponsorships()
    {
        return $this->belongsToMany(Sponsorship::class, 'sponsorship_groups');
    }
}
