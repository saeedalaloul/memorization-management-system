<?php

namespace Database\Seeders;

use App\Models\QuranPart;
use App\Models\QuranSummativePart;
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
            'الجزء الأول (البقرة)',
            'الجزء الثاني (البقرة)',
            'الجزء الثالث (البقرة)',
            'الجزء الرابع (آل عمران)',
            'الجزء الخامس (النساء)',
            'الجزء السادس (المائدة)',
            'الجزء السابع (الأنعام)',
            'الجزء الثامن (الأعراف)',
            'الجزء التاسع (الأنفال)',
            'الجزء العاشر (التوبة)',
            'الجزء الحادي عشر (يونس)',
            'الجزء الثاني عشر (هود)',
            'الجزء الثالث عشر (يوسف)',
            'الجزء الرابع عشر (النحل)',
            'الجزء الخامس عشر (الإسراء)',
            'الجزء السادس عشر (مريم)',
            'الجزء السابع عشر (الأنبياء)',
            'الجزء الثامن عشر (المؤمنون)',
            'الجزء التاسع عشر (الفرقان)',
            'الجزء العشرون (النمل)',
            'الجزء الحادي والعشرون (العنكبوت)',
            'الجزء الثاني والعشرون (الأحزاب)',
            'الجزء الثالث والعشرون (يس)',
            'الجزء الرابع والعشرون (الزمر)',
            'الجزء الخامس والعشرون (الشورى)',
            'الجزء السادس والعشرون (الأحقاف)',
            'الجزء السابع والعشرون (الذاريات)',
            'الجزء الثامن والعشرون (قد سمع)',
            'الجزء التاسع والعشرون (تبارك)',
            'الجزء الثلاثين (عم)',
        ];

        foreach ($quran_parts as $quran_part) {
            QuranPart::create(['name' => $quran_part]);
        }

        $quran_summative_parts = [
            ['name' => 'الدورة الأولى', 'number_parts' => 3, 'description' => 'المجادلة – الناس'],
            ['name' => 'الدورة الثانية', 'number_parts' => 5, 'description' => 'الأحقاف – الناس'],
            ['name' => 'الدورة الثالثة', 'number_parts' => 3, 'description' => 'يس – الجاثية'],
            ['name' => 'الدورة الرابعة', 'number_parts' => 5, 'description' => 'العنكبوت – الجاثية'],
            ['name' => 'الاختبار النهائي', 'number_parts' => 10, 'description' => 'العنكبوت إلى الناس'],
            ['name' => 'الدورة الأولى', 'number_parts' => 3, 'description' => 'المؤمنون - القصص'],
            ['name' => 'الدورة الثانية', 'number_parts' => 5, 'description' => 'مريم – القصص'],
            ['name' => 'الدورة الثالثة', 'number_parts' => 3, 'description' => 'يوسف - الكهف'],
            ['name' => 'الدورة الرابعة', 'number_parts' => 5, 'description' => 'يونس - الكهف'],
            ['name' => 'الاختبار النهائي', 'number_parts' => 10, 'description' => 'يونس إلى القصص'],
            ['name' => 'الدورة الأولى', 'number_parts' => 3, 'description' => 'الأعراف - التوبة'],
            ['name' => 'الدورة الثانية', 'number_parts' => 5, 'description' => 'المائدة – التوبة'],
            ['name' => 'الدورة الثالثة', 'number_parts' => 3, 'description' => 'آل عمران - النساء'],
            ['name' => 'الدورة الرابعة', 'number_parts' => 5, 'description' => 'البقرة - النساء'],
            ['name' => 'الاختبار النهائي', 'number_parts' => 10, 'description' => 'البقرة إلى التوبة'],
            ['name' => 'الاختبار النهائي', 'number_parts' => 15, 'description' => 'ومن ثم ينتقل الطالب لمنتدي الحفاظ'],
        ];

        foreach ($quran_summative_parts as $key => $value) {
            QuranSummativePart::create(['name' => $value['name'], 'number_parts' => $value['number_parts'], 'description' => $value['description']]);
        }
    }
}
