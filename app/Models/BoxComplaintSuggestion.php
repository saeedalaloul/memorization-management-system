<?php

namespace App\Models;


use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class BoxComplaintSuggestion extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'datetime',
        'category',
        'subject',
        'sender_id',
        'receiver_id',
        'reply',
        'subject_read_at',
        'reply_read_at',
    ];

    const COMPLAINT_CATEGORY = "complaint";
    const SUGGESTION_CATEGORY = "suggestion";
    const IDEA_CATEGORY = "idea";

    public static function categories()
    {
        return [
            self::COMPLAINT_CATEGORY => 'شكوى',
            self::SUGGESTION_CATEGORY => 'اقتراح',
            self::IDEA_CATEGORY => 'فكرة',
        ];
    }


    // علاقة بين صندوق الشكاوي والإقتراحات والمستخدمين لجلب اسم المستخدم في جدول صندوق الشكاوي والإقتراحات
    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id');
    }

    // علاقة بين صندوق الشكاوي والإقتراحات والمستخدمين لجلب اسم المستخدم في جدول صندوق الشكاوي والإقتراحات
    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'receiver_id');
    }
}
