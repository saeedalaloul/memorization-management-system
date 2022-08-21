<?php

namespace App\Models;


use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class VisitOrder extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'hostable_type',
        'hostable_id',
        'datetime',
        'oversight_member_id',
        'status',
        'notes',
        'suggestions',
        'recommendations',
    ];

    const IN_PENDING_STATUS = "in-pending";
    const IN_SENDING_STATUS = "in-sending";
    const IN_APPROVAL_STATUS = "in-approval";

    public static function status()
    {
        return [
            self::IN_PENDING_STATUS => 'في انتظار الزيارة',
            self::IN_SENDING_STATUS => 'في انتظار الإرسال',
            self::IN_APPROVAL_STATUS => 'في انتظار الإعتماد',
        ];
    }

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('oversight_member', function ($q) use ($val) {
                $q->whereHas('user', function ($q) use ($val) {
                    $q->where('name', 'LIKE', "%$val%");
                });
            });
    }

    public function scopeTodayVisits($query)
    {
        return $query
            ->whereDate('datetime', date('Y-m-d', time()))
            ->where('status', self::IN_PENDING_STATUS);
    }

    public function hostable()
    {
        return $this->morphTo();
    }

    public function oversight_member()
    {
        return $this->belongsTo('App\Models\OversightMember', 'oversight_member_id');
    }
}
