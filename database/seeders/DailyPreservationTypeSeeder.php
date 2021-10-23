<?php

namespace Database\Seeders;

use App\Models\DailyPreservationType;
use Illuminate\Database\Seeder;

class DailyPreservationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $DailyPreservationTypes = [
            'حفظ',
            'مراجعة',
        ];

        foreach ($DailyPreservationTypes as $DailyPreservationType) {
            DailyPreservationType::create(['name' => $DailyPreservationType]);
        }
    }
}
