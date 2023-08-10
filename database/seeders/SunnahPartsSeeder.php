<?php

namespace Database\Seeders;

use App\Models\SunnahPart;
use Illuminate\Database\Seeder;

class SunnahPartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $sunnah_parts = [
            ['name' => '(1) 30-1', 'total_hadith_parts' => 30, 'sunnah_book_id' => 1, 'arrangement' => 1, 'type' => 'individual'],
            ['name' => '(1) 60-31', 'total_hadith_parts' => 30, 'sunnah_book_id' => 1, 'arrangement' => 2, 'type' => 'individual'],
            ['name' => '(1) 90-61', 'total_hadith_parts' => 30, 'sunnah_book_id' => 1, 'arrangement' => 3, 'type' => 'individual'],
            ['name' => '(1) 90-1', 'total_hadith_parts' => 90, 'sunnah_book_id' => 1, 'arrangement' => 4, 'type' => 'deserved'],
            ['name' => '(1) 130-91', 'total_hadith_parts' => 40, 'sunnah_book_id' => 1, 'arrangement' => 5, 'type' => 'individual'],
            ['name' => '(1) 170-131', 'total_hadith_parts' => 40, 'sunnah_book_id' => 1, 'arrangement' => 6, 'type' => 'individual'],
            ['name' => '(1) 170-91', 'total_hadith_parts' => 80, 'sunnah_book_id' => 1, 'arrangement' => 7, 'type' => 'deserved'],
            ['name' => '(1) 210-171', 'total_hadith_parts' => 40, 'sunnah_book_id' => 1, 'arrangement' => 8, 'type' => 'individual'],
            ['name' => '(1) 250-211', 'total_hadith_parts' => 40, 'sunnah_book_id' => 1, 'arrangement' => 9, 'type' => 'individual'],
            ['name' => '(1) 250-171', 'total_hadith_parts' => 80, 'sunnah_book_id' => 1, 'arrangement' => 10, 'type' => 'deserved'],
            ['name' => '(1) 280-251', 'total_hadith_parts' => 30, 'sunnah_book_id' => 1, 'arrangement' => 11, 'type' => 'individual'],
            ['name' => '(1) 310-281', 'total_hadith_parts' => 30, 'sunnah_book_id' => 1, 'arrangement' => 12, 'type' => 'individual'],
            ['name' => '(1) 340-311', 'total_hadith_parts' => 30, 'sunnah_book_id' => 1, 'arrangement' => 13, 'type' => 'individual'],
            ['name' => '(1) 340-251', 'total_hadith_parts' => 90, 'sunnah_book_id' => 1, 'arrangement' => 14, 'type' => 'deserved'],
            ['name' => '(1) 380-341', 'total_hadith_parts' => 40, 'sunnah_book_id' => 1, 'arrangement' => 15, 'type' => 'individual'],
            ['name' => '(1) 420-381', 'total_hadith_parts' => 40, 'sunnah_book_id' => 1, 'arrangement' => 16, 'type' => 'individual'],
            ['name' => '(1) 420-341', 'total_hadith_parts' => 80, 'sunnah_book_id' => 1, 'arrangement' => 17, 'type' => 'deserved'],
            ['name' => '(1) 460-421', 'total_hadith_parts' => 40, 'sunnah_book_id' => 1, 'arrangement' => 18, 'type' => 'individual'],
            ['name' => '(1) 500-461', 'total_hadith_parts' => 40, 'sunnah_book_id' => 1, 'arrangement' => 19, 'type' => 'individual'],
            ['name' => '(1) 500-421', 'total_hadith_parts' => 80, 'sunnah_book_id' => 1, 'arrangement' => 20, 'type' => 'deserved'],
        ////////////////////////////////////////////////////////////////////////////////////////////////
            ['name' => '(2) 30-1', 'total_hadith_parts' => 30, 'sunnah_book_id' => 2, 'arrangement' => 21, 'type' => 'individual'],
            ['name' => '(2) 60-31', 'total_hadith_parts' => 30, 'sunnah_book_id' => 2, 'arrangement' => 22, 'type' => 'individual'],
            ['name' => '(2) 90-61', 'total_hadith_parts' => 30, 'sunnah_book_id' => 2, 'arrangement' => 23, 'type' => 'individual'],
            ['name' => '(2) 90-1', 'total_hadith_parts' => 90, 'sunnah_book_id' => 2, 'arrangement' => 24, 'type' => 'deserved'],
            ['name' => '(2) 130-91', 'total_hadith_parts' => 40, 'sunnah_book_id' => 2, 'arrangement' => 25, 'type' => 'individual'],
            ['name' => '(2) 170-131', 'total_hadith_parts' => 40, 'sunnah_book_id' => 2, 'arrangement' => 26, 'type' => 'individual'],
            ['name' => '(2) 170-91', 'total_hadith_parts' => 80, 'sunnah_book_id' => 2, 'arrangement' => 27, 'type' => 'deserved'],
            ['name' => '(2) 210-171', 'total_hadith_parts' => 40, 'sunnah_book_id' => 2, 'arrangement' => 28, 'type' => 'individual'],
            ['name' => '(2) 250-211', 'total_hadith_parts' => 40, 'sunnah_book_id' => 2, 'arrangement' => 29, 'type' => 'individual'],
            ['name' => '(2) 250-171', 'total_hadith_parts' => 80, 'sunnah_book_id' => 2, 'arrangement' => 30, 'type' => 'deserved'],
            ['name' => '(2) 280-251', 'total_hadith_parts' => 30, 'sunnah_book_id' => 2, 'arrangement' => 31, 'type' => 'individual'],
            ['name' => '(2) 310-281', 'total_hadith_parts' => 30, 'sunnah_book_id' => 2, 'arrangement' => 32, 'type' => 'individual'],
            ['name' => '(2) 340-311', 'total_hadith_parts' => 30, 'sunnah_book_id' => 2, 'arrangement' => 33, 'type' => 'individual'],
            ['name' => '(2) 340-251', 'total_hadith_parts' => 90, 'sunnah_book_id' => 2, 'arrangement' => 34, 'type' => 'deserved'],
            ['name' => '(2) 380-341', 'total_hadith_parts' => 40, 'sunnah_book_id' => 2, 'arrangement' => 35, 'type' => 'individual'],
            ['name' => '(2) 420-381', 'total_hadith_parts' => 40, 'sunnah_book_id' => 2, 'arrangement' => 36, 'type' => 'individual'],
            ['name' => '(2) 420-341', 'total_hadith_parts' => 80, 'sunnah_book_id' => 2, 'arrangement' => 37, 'type' => 'deserved'],
            ////////////////////////////////////////////////////
            ['name' => '(3) 40-1', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 38, 'type' => 'individual'],
            ['name' => '(3) 80-41', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 39, 'type' => 'individual'],
            ['name' => '(3) 80-1', 'total_hadith_parts' => 80, 'sunnah_book_id' => 3, 'arrangement' => 40, 'type' => 'deserved'],
            ['name' => '(3) 110-81', 'total_hadith_parts' => 30, 'sunnah_book_id' => 3, 'arrangement' => 41, 'type' => 'individual'],
            ['name' => '(3) 140-111', 'total_hadith_parts' => 30, 'sunnah_book_id' => 3, 'arrangement' => 42, 'type' => 'individual'],
            ['name' => '(3) 170-141', 'total_hadith_parts' => 30, 'sunnah_book_id' => 3, 'arrangement' => 43, 'type' => 'individual'],
            ['name' => '(3) 170-81', 'total_hadith_parts' => 90, 'sunnah_book_id' => 3, 'arrangement' => 44, 'type' => 'deserved'],
            ['name' => '(3) 210-171', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 45, 'type' => 'individual'],
            ['name' => '(3) 250-211', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 46, 'type' => 'individual'],
            ['name' => '(3) 250-171', 'total_hadith_parts' => 80, 'sunnah_book_id' => 3, 'arrangement' => 47, 'type' => 'deserved'],
            ['name' => '(3) 290-251', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 48, 'type' => 'individual'],
            ['name' => '(3) 330-291', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 49, 'type' => 'individual'],
            ['name' => '(3) 330-251', 'total_hadith_parts' => 80, 'sunnah_book_id' => 3, 'arrangement' => 50, 'type' => 'deserved'],
            ['name' => '(3) 360-331', 'total_hadith_parts' => 30, 'sunnah_book_id' => 3, 'arrangement' => 51, 'type' => 'individual'],
            ['name' => '(3) 390-361', 'total_hadith_parts' => 30, 'sunnah_book_id' => 3, 'arrangement' => 52, 'type' => 'individual'],
            ['name' => '(3) 420-391', 'total_hadith_parts' => 30, 'sunnah_book_id' => 3, 'arrangement' => 53, 'type' => 'individual'],
            ['name' => '(3) 420-331', 'total_hadith_parts' => 90, 'sunnah_book_id' => 3, 'arrangement' => 54, 'type' => 'deserved'],
            ['name' => '(3) 460-421', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 55, 'type' => 'individual'],
            ['name' => '(3) 500-461', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 56, 'type' => 'individual'],
            ['name' => '(3) 500-421', 'total_hadith_parts' => 80, 'sunnah_book_id' => 3, 'arrangement' => 57, 'type' => 'deserved'],
            ['name' => '(3) 540-501', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 58, 'type' => 'individual'],
//            ['name' => '(3+4) 25-541', 'total_hadith_parts' => 40, 'sunnah_book_id' => 3, 'arrangement' => 59, 'type' => 'individual'],
//            ['name' => '(3+4) 25-501', 'total_hadith_parts' => 80, 'sunnah_book_id' => 3, 'arrangement' => 60, 'type' => 'deserved'],
        ];

        foreach ($sunnah_parts as $key => $value) {
            SunnahPart::create(['name' => $value['name'], 'total_hadith_parts' => $value['total_hadith_parts'], 'sunnah_book_id' => $value['sunnah_book_id'], 'arrangement' => $value['arrangement'], 'type' => $value['type']]);
        }
    }
}
