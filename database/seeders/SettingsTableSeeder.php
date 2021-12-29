<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('settings')->delete();

        $data = [
            ['key' => 'current_session', 'value' => '2021-2022'],
            ['key' => 'center_title', 'value' => 'MS'],
            ['key' => 'center_name', 'value' => 'Memorization Management System'],
            ['key' => 'end_first_term', 'value' => '01-12-2021'],
            ['key' => 'end_second_term', 'value' => '01-03-2022'],
            ['key' => 'phone', 'value' => '0599999999'],
            ['key' => 'address', 'value' => 'غزة'],
            ['key' => 'center_email', 'value' => 'memorization@memorization.com'],
            ['key' => 'logo', 'value' => 'logo.jpg'],
        ];

        DB::table('settings')->insert($data);
    }
}
