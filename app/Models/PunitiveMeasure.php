<?php

namespace App\Models;

use Adnane\SimpleUuid\Traits\SimpleUuid;
use Illuminate\Database\Eloquent\Model;

class PunitiveMeasure extends Model
{
    use SimpleUuid;

    protected $fillable = [
        'type',
        'reason',
        'number_times',
        'quantity',
    ];

    const BLOCK_TYPE = "block";
    const WARNING_TYPE = "warning";

    const MEMORIZE_REASON = "memorize";
    const DID_NOT_MEMORIZE_REASON = "did-not-memorize";
    const ABSENCE_REASON = "absence";
    const LATE_REASON = "late";

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
        ];
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'punitive_measure_groups');
    }
}
