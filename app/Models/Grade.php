<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Grade extends Model
{
    use HasFactory,SimpleUuid;
    protected $fillable =['name'];

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->Orwhere('name', 'like', '%' . $val . '%');
    }

    public function groups(){
        return $this->hasMany('App\Models\Group');
    }
}
