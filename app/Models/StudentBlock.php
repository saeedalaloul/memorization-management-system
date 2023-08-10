<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class StudentBlock extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'student_id',
        'block_expiry_date',
        'reason',
        'details',
        'notes',
    ];

    public const MEMORIZE_REASON = "memorize";
    public const DID_NOT_MEMORIZE_REASON = "did-not-memorize";
    public const ABSENCE_REASON = "absence";
    public const LATE_REASON = "late";
    public const AUTHORIZED_REASON = "authorized";


    public static function reasons()
    {
        return [
            self::MEMORIZE_REASON => 'ضعف كمية الحفظ',
            self::DID_NOT_MEMORIZE_REASON => 'لم يحفظ',
            self::ABSENCE_REASON => 'الغياب',
            self::LATE_REASON => 'التأخر',
            self::AUTHORIZED_REASON => 'مأذون',
        ];
    }


    /**
     * @throws \JsonException
     */
    public function getDetailsAttribute($details)
    {
        if ($this->reason === self::MEMORIZE_REASON) {
            return "لقد تم حظر عمليات الطالب بسبب تسميعه المتكرر أقل من " . json_decode($details, false, 512, JSON_THROW_ON_ERROR)->number_pages . " صفحة لمدة " . json_decode($details)->number_times . " أيام,راجع أمير المركز!";
        }

        if ($this->reason === self::DID_NOT_MEMORIZE_REASON) {
            return "لقد تم حظر عمليات الطالب بسبب عدم الحفظ المتكرر لمدة " . json_decode($details, false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع أمير المركز!";
        }

        if ($this->reason === self::ABSENCE_REASON) {
            return "لقد تم حظر عمليات الطالب بسبب غيابه المتكرر لمدة " . json_decode($details, false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع أمير المركز!";
        }

        if ($this->reason === self::LATE_REASON) {
            return "لقد تم حظر عمليات الطالب بسبب تأخره المتكرر لمدة " . json_decode($details, false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع أمير المركز!";
        }
        return "";
    }


    // علاقة بين الحظر والطلاب لجلب اسم الطالب في جدول الحظر
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }
}
