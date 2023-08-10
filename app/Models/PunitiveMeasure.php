<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;

class PunitiveMeasure extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'type',
        'reason',
        'number_times',
        'quantity',
    ];

    public const BLOCK_TYPE = "block";
    public const WARNING_TYPE = "warning";

    public const MEMORIZE_REASON = "memorize";
    public const DID_NOT_MEMORIZE_REASON = "did-not-memorize";
    public const ABSENCE_REASON = "absence";
    public const LATE_REASON = "late";
    public const AUTHORIZED_REASON = "authorized";

    public static function types(){
        return [
            self::BLOCK_TYPE => 'حظر',
            self::WARNING_TYPE => 'إنذار',
        ];
    }

    public static function reasons(){
        return [
            self::MEMORIZE_REASON => 'ضعف كمية الحفظ',
            self::DID_NOT_MEMORIZE_REASON => 'لم يحفظ',
            self::ABSENCE_REASON => 'الغياب',
            self::LATE_REASON => 'التأخر',
            self::AUTHORIZED_REASON => 'مأذون',
        ];
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'punitive_measure_groups');
    }
}
