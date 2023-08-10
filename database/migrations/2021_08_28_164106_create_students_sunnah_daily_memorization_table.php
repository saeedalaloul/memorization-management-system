<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsSunnahDailyMemorizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_sunnah_daily_memorization', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('student_id')->index()->references('id')->on('students')->restrictOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->restrictOnDelete();
            $table->enum('type',['memorize','review'])->index();
            $table->foreignId('book_id')->index()->references('id')->on('sunnah_books')->restrictOnDelete();
            $table->unsignedSmallInteger('hadith_from')->index();
            $table->unsignedSmallInteger('hadith_to')->index();
            $table->enum('evaluation',['excellent','very-good','good','weak'])->index();
            $table->dateTime('datetime')->index();
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students_sunnah_daily_memorization');
    }
}
