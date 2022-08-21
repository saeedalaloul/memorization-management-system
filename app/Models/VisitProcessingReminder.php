<?php

namespace App\Models;


use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class VisitProcessingReminder extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'id',
        'reminder_datetime',
    ];

    public function visit()
    {
        return $this->hasOne('App\Models\Visit', 'id');
    }
}
