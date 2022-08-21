<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFcmToken extends Model
{
    protected $fillable = ['id','device_token'];



    // علاقة بين معلومات المستخدم والمستخدمين لجلب معلومات المستخدم في جدول معلومات المستخدمين
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }
}
