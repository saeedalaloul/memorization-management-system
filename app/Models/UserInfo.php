<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $fillable = ['id','economic_situation','recitation_level','academic_qualification'];

    const GOOD_STATUS = "good";
    const MODERATE_STATUS = "moderate";
    const DIFFICULT_STATUS = "difficult";

    const AL_NOORANIAH_LEVEL = "al-qaida-al-nooraniah";
    const QUALIFYING_LEVEL = "qualifying";
    const HIGH_LEVEL = "high";
    const TAHIL_ALSANAD_LEVEL = "tahil-alsanad";
    const SANAD_LEVEL = "sanad";

    public $timestamps = false;

    public static function status()
    {
        return [
            self::GOOD_STATUS => 'جيد',
            self::MODERATE_STATUS => 'متوسط',
            self::DIFFICULT_STATUS => 'صعب',
        ];
    }

    public static function levels()
    {
        return [
            self::AL_NOORANIAH_LEVEL => 'القاعدة النورانية',
            self::QUALIFYING_LEVEL => 'التأهيلية',
            self::HIGH_LEVEL => 'العليا',
            self::TAHIL_ALSANAD_LEVEL => 'تأهيل السند',
            self::SANAD_LEVEL => 'سند',
        ];
    }



    // علاقة بين معلومات المستخدم والمستخدمين لجلب معلومات المستخدم في جدول معلومات المستخدمين
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }
}
