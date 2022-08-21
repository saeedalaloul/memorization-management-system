<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class ActivityOrder extends Model
{
    use SimpleUuid;

    protected $fillable = ['datetime', 'status', 'activity_type_id', 'teacher_id', 'activity_member_id', 'notes'];

    const IN_PENDING_STATUS = "in-pending";
    const REJECTED_STATUS = "rejected";
    const ACCEPTABLE_STATUS = "acceptable";
    const FAILURE_STATUS = "failure";

    public static function status()
    {
        return [
            self::IN_PENDING_STATUS => 'قيد الطلب',
            self::REJECTED_STATUS => 'مرفوض',
            self::ACCEPTABLE_STATUS => 'معتمد',
            self::FAILURE_STATUS => 'لم يتم إجراء النشاط',
        ];
    }

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
        return $this->belongsToMany('App\Models\Student', 'activity_order_students');
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
