<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    protected $fillable = ['name', 'place', 'start_datetime', 'end_datetime'];

    public function scopeSearch($query, $val)
    {
        return $query->where('name', 'like', '%' . $val . '%');
    }


    public function activities()
    {
        return $this->hasMany('App\Models\Activity', 'activity_type_id', 'id');
    }

    public function activities_orders()
    {
        return $this->hasMany('App\Models\ActivityOrder', 'activity_type_id', 'id')
            ->where('teacher_id', '=', auth()->id());
    }

    public function activities_orders_types()
    {
        return $this->hasMany('App\Models\ActivityOrder', 'activity_type_id', 'id');
    }

}
