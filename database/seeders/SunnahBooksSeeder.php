<?php

namespace Database\Seeders;

use App\Models\QuranSuras;
use App\Models\SunnahBooks;
use Illuminate\Database\Seeder;

class SunnahBooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sunnah_books = [
            ['name' => 'المنتخب من رياض الصالحين', 'total_number_hadith' => 500,],
            ['name' => 'عمدة الأحكام من كلام خير الأنام', 'total_number_hadith' => 420,],
            ['name' => 'الأدب العالي', 'total_number_hadith' => 550,],
            ['name' => 'رياض الصالحين', 'total_number_hadith' => 1000,],
        ];

        foreach ($sunnah_books as $sunnah_book) {
            SunnahBooks::create($sunnah_book);
        }
    }
}
