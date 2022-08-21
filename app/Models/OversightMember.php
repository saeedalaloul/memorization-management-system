<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OversightMember extends Model
{
    protected $fillable = ['id'];

    const CACHE_KEY = "oversight_members";


    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('user', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            });
    }

    // علاقة بين المراقبين والمستخدمين لجلب اسم المراقب في جدول المراقبين
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    // علاقة بين المراقبين وجدول الزيارات لجلب عدد الزيارات في جدول المراقبين
    public function visits()
    {
        return $this->hasMany('App\Models\Visit', 'oversight_member_id', 'id');
    }

    // علاقة بين المراقبين وجدول طلبات الزيارات لجلب عدد طلبات الزيارات في جدول المراقبين
    public function visits_orders()
    {
        return $this->hasMany('App\Models\VisitOrder', 'oversight_member_id', 'id');
    }
}
