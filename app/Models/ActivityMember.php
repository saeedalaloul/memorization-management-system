<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityMember extends Model
{
    protected $fillable = ['id'];
    const CACHE_KEY = "activity_members";

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('user', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            });
    }

    // علاقة بين المنشطين والمستخدمين لجلب اسم المنشط في جدول المنشطين
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

//    // علاقة بين المنشطين وجدول الزيارات لجلب عدد الأنشطة في جدول المنشطين
    public function activities()
    {
        return $this->hasMany('App\Models\Activity', 'activity_member_id', 'id');
    }

//    // علاقة بين المنشطين وجدول طلبات الأنشطة لجلب عدد طلبات الأنشطة في جدول المنشطين
    public function activities_orders()
    {
        return $this->hasMany('App\Models\ActivityOrder', 'activity_member_id', 'id');
    }

    //    // علاقة بين المنشطين وجدول طلبات الأنشطة لجلب عدد طلبات الأنشطة في جدول المنشطين
    public function activities_orders_acceptable()
    {
        return $this->hasMany('App\Models\ActivityOrder', 'activity_member_id', 'id')
            ->where('status', '=', ActivityOrder::ACCEPTABLE_STATUS);
    }

    // علاقة بين المنشطين والزيارات لجلب طلبات الزيارات في جدول المنشطين

    public function visit_orders()
    {
        return $this->morphMany(VisitOrder::class, 'hostable');
    }
}
