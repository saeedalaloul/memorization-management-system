<?php

namespace Database\Seeders;

use App\Models\DailyPreservationEvaluation;
use Illuminate\Database\Seeder;

class DailyPreservationEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $DailyPreservationEvaluations = [
            'ممتاز',
            'جيد جدا',
            'جيد',
            'ضعيف',
        ];

        foreach ($DailyPreservationEvaluations as $DailyPreservationEvaluation) {
            DailyPreservationEvaluation::create(['name' => $DailyPreservationEvaluation]);
        }
    }
}
