<?php

namespace App\Models;


use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'hostable_type',
        'hostable_id',
        'datetime',
        'oversight_member_id',
        'status',
        'reply',
        'notes',
        'suggestions',
        'recommendations',
    ];

    const IN_PENDING_STATUS = "in-pending";
    const REPLIED_STATUS = "replied";
    const IN_PROCESS_STATUS = "in-process";
    const FAILURE_STATUS = "failure";
    const SOLVED_STATUS = "solved";

    public static function status()
    {
        return [
            self::IN_PENDING_STATUS => 'مطلوب الرد',
            self::REPLIED_STATUS => 'تم الرد',
            self::IN_PROCESS_STATUS => 'في انتظار المعالجة',
            self::FAILURE_STATUS => 'فشل المعالجة',
            self::SOLVED_STATUS => 'تم الحل',
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

    public function hostable()
    {
        return $this->morphTo();
    }

    public function oversight_member()
    {
        return $this->belongsTo('App\Models\OversightMember', 'oversight_member_id');
    }

    public function visit_processing_reminder()
    {
        return $this->belongsTo('App\Models\VisitProcessingReminder', 'id');
    }
}
