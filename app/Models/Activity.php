<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use  SimpleUuid;

    protected $fillable = ['datetime', 'activity_type_id', 'teacher_id', 'activity_member_id', 'notes'];

    public function scopeSearch($query, $val)
    {
        return $query->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('teacher', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            })->OrwhereHas('activity_type', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            });
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'activity_students');
    }

    public function activity_type()
    {
        return $this->belongsTo('App\Models\ActivityType', 'activity_type_id');
    }

    public function activity_member()
    {
        return $this->belongsTo('App\Models\ActivityMember', 'activity_member_id');
    }

    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher', 'teacher_id');
    }

}
