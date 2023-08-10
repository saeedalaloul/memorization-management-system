<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\Group;
use App\Models\Student;

class Grade extends Model
{
    use SimpleUuid;

    protected $fillable = ['name','section'];

    public const MALE_SECTION = "male";
    public const FEMALE_SECTION = "female";

    public static function sections()
    {
        return [
            self::MALE_SECTION => 'قسم الطلاب',
            self::FEMALE_SECTION => 'قسم الطالبات',
        ];
    }

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->Orwhere('name', 'like', '%' . $val . '%');
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
