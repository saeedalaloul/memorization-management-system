<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use SimpleUuid;

    protected $fillable = ['name'];

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->Orwhere('name', 'like', '%' . $val . '%');
    }

    public function teachers()
    {
        return $this->hasMany('App\Models\Teacher');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Group');
    }
}
