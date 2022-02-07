<?php

namespace App\Models;


use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxComplaintSuggestion extends Model
{
    use HasFactory, SimpleUuid;

    protected $fillable = [
        'complaint_date',
        'category_complaint_id',
        'subject',
        'sender_id',
        'receiver_id',
        'receiver_role_id',
        'reply',
        'read_at',
    ];

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

    // علاقة بين صندوق الشكاوي والإقتراحات وأدواره لجلب اسم الدور في جدول صندوق الشكاوي والإقتراحات
    public function receiver_role()
    {
        return $this->belongsTo('App\Models\ComplaintBoxRole', 'receiver_role_id');
    }

    // علاقة بين صندوق الشكاوي والإقتراحات وتصنيفاته لجلب اسم وتصنيفاته في جدول صندوق الشكاوي والإقتراحات
    public function category_complaint()
    {
        return $this->belongsTo('App\Models\ComplaintBoxCategory', 'category_complaint_id');
    }
}
