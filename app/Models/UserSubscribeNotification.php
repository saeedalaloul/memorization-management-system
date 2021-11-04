<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscribeNotification extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'player_id'];
    public $timestamps = false;

    // علاقة بين المحفظين والمستخدمين لجلب اسم المحفظ في جدول المحفظين
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }
}
