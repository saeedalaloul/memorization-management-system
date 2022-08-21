<?php

namespace Database\Seeders;

use App\Models\QuranPart;
use Illuminate\Database\Seeder;

class QuranPartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $quran_parts = [
            ['name' => '10-1', 'arrangement' => 44, 'total_preservation_parts' => 10, 'type' => 'deserved', 'description' => 'الفاتحة-التوبة'],
            ['name' => '5-1', 'arrangement' => 43, 'total_preservation_parts' => 5, 'type' => 'deserved', 'description' => 'الفاتحة-النساء'],
            ['name' => '5-3', 'arrangement' => 40, 'total_preservation_parts' => 3, 'type' => 'deserved', 'description' => 'آل عمران-النساء'],
            ['name' => '10-6', 'arrangement' => 37, 'total_preservation_parts' => 5, 'type' => 'deserved', 'description' => 'المائدة-التوبة'],
            ['name' => '10-8', 'arrangement' => 34, 'total_preservation_parts' => 3, 'type' => 'deserved', 'description' => 'الأعراف-التوبة'],
            ['name' => '20-11', 'arrangement' => 30, 'total_preservation_parts' => 10, 'type' => 'deserved', 'description' => 'يونس-القصص'],
            ['name' => '15-11', 'arrangement' => 29, 'total_preservation_parts' => 5, 'type' => 'deserved', 'description' => 'يونس-الكهف'],
            ['name' => '15-13', 'arrangement' => 26, 'total_preservation_parts' => 3, 'type' => 'deserved', 'description' => 'يوسف-الكهف'],
            ['name' => '20-16', 'arrangement' => 22, 'total_preservation_parts' => 5, 'type' => 'deserved', 'description' => 'مريم-القصص'],
            ['name' => '20-18', 'arrangement' => 19, 'total_preservation_parts' => 3, 'type' => 'deserved', 'description' => 'المؤمنون-القصص'],
            ['name' => '30-21', 'arrangement' => 15, 'total_preservation_parts' => 10, 'type' => 'deserved', 'description' => 'العنكبوت-الناس'],
            ['name' => '25-21', 'arrangement' => 14, 'total_preservation_parts' => 5, 'type' => 'deserved', 'description' => 'العنكبوت-الجاثية'],
            ['name' => '25-23', 'arrangement' => 11, 'total_preservation_parts' => 3, 'type' => 'deserved', 'description' => 'يس-الجاثية'],
            ['name' => '30-26', 'arrangement' => 7, 'total_preservation_parts' => 5, 'type' => 'deserved', 'description' => 'الأحقاف-الناس'],
            ['name' => '30-28', 'arrangement' => 4, 'total_preservation_parts' => 3, 'type' => 'deserved', 'description' => 'المجادلة-الناس'],
            ['name' => '1', 'arrangement' => 42, 'total_preservation_parts' => 30, 'type' => 'individual', 'description' => 'البقرة'],
            ['name' => '2', 'arrangement' => 41, 'total_preservation_parts' => 29, 'type' => 'individual', 'description' => 'البقرة'],
            ['name' => '4+3', 'arrangement' => 39, 'total_preservation_parts' => 28, 'type' => 'individual', 'description' => 'آل عمران'],
            ['name' => '5', 'arrangement' => 38, 'total_preservation_parts' => 26, 'type' => 'individual', 'description' => 'النساء'],
            ['name' => '6', 'arrangement' => 36, 'total_preservation_parts' => 25, 'type' => 'individual', 'description' => 'المائدة'],
            ['name' => '7', 'arrangement' => 35, 'total_preservation_parts' => 24, 'type' => 'individual', 'description' => 'الأنعام'],
            ['name' => '8', 'arrangement' => 33, 'total_preservation_parts' => 23, 'type' => 'individual', 'description' => 'الأعراف'],
            ['name' => '9', 'arrangement' => 32, 'total_preservation_parts' => 22, 'type' => 'individual', 'description' => 'الأنفال'],
            ['name' => '10', 'arrangement' => 31, 'total_preservation_parts' => 21, 'type' => 'individual', 'description' => 'التوبة'],
            ['name' => '11', 'arrangement' => 28, 'total_preservation_parts' => 20, 'type' => 'individual', 'description' => 'يونس'],
            ['name' => '12', 'arrangement' => 27, 'total_preservation_parts' => 19, 'type' => 'individual', 'description' => 'هود'],
            ['name' => '13', 'arrangement' => 25, 'total_preservation_parts' => 18, 'type' => 'individual', 'description' => 'يوسف'],
            ['name' => '14', 'arrangement' => 24, 'total_preservation_parts' => 17, 'type' => 'individual', 'description' => 'النحل'],
            ['name' => '15', 'arrangement' => 23, 'total_preservation_parts' => 16, 'type' => 'individual', 'description' => 'الكهف'],
            ['name' => '16', 'arrangement' => 21, 'total_preservation_parts' => 15, 'type' => 'individual', 'description' => 'مريم'],
            ['name' => '17', 'arrangement' => 20, 'total_preservation_parts' => 14, 'type' => 'individual', 'description' => 'الأنبياء'],
            ['name' => '18', 'arrangement' => 18, 'total_preservation_parts' => 13, 'type' => 'individual', 'description' => 'النور'],
            ['name' => '19', 'arrangement' => 17, 'total_preservation_parts' => 12, 'type' => 'individual', 'description' => 'الشعراء'],
            ['name' => '20', 'arrangement' => 16, 'total_preservation_parts' => 11, 'type' => 'individual', 'description' => 'القصص'],
            ['name' => '21', 'arrangement' => 13, 'total_preservation_parts' => 10, 'type' => 'individual', 'description' => 'العنكبوت'],
            ['name' => '22', 'arrangement' => 12, 'total_preservation_parts' => 9, 'type' => 'individual', 'description' => 'الأحزاب'],
            ['name' => '23', 'arrangement' => 10, 'total_preservation_parts' => 8, 'type' => 'individual', 'description' => 'يس'],
            ['name' => '24', 'arrangement' => 9, 'total_preservation_parts' => 7, 'type' => 'individual', 'description' => 'الزمر'],
            ['name' => '25', 'arrangement' => 8, 'total_preservation_parts' => 6, 'type' => 'individual', 'description' => 'الشورى'],
            ['name' => '26', 'arrangement' => 6, 'total_preservation_parts' => 5, 'type' => 'individual', 'description' => 'الأحقاف'],
            ['name' => '27', 'arrangement' => 5, 'total_preservation_parts' => 4, 'type' => 'individual', 'description' => 'الذاريات'],
            ['name' => '28', 'arrangement' => 3, 'total_preservation_parts' => 3, 'type' => 'individual', 'description' => 'قد سمع'],
            ['name' => '29', 'arrangement' => 2, 'total_preservation_parts' => 2, 'type' => 'individual', 'description' => 'تبارك'],
            ['name' => '30', 'arrangement' => 1, 'total_preservation_parts' => 1, 'type' => 'individual', 'description' => 'عم'],
        ];

        foreach ($quran_parts as $key => $value) {
            QuranPart::create(['name' => $value['name'], 'arrangement' => $value['arrangement'], 'total_preservation_parts' => $value['total_preservation_parts'], 'type' => $value['type'], 'description' => $value['description']]);
        }
    }
}
