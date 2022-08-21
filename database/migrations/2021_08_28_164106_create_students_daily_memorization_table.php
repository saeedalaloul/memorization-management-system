<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsDailyMemorizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_daily_memorization', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('student_id')->index()->references('id')->on('students')->restrictOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->restrictOnDelete();
            $table->enum('type',['memorize','review','cumulative-review'])->index();
            $table->foreignId('sura_from_id')->index()->references('id')->on('quran_suras')->restrictOnDelete();
            $table->foreignId('sura_to_id')->index()->references('id')->on('quran_suras')->restrictOnDelete();
            $table->unsignedSmallInteger('aya_from')->index();
            $table->unsignedSmallInteger('aya_to')->index();
            $table->unsignedDecimal('number_pages',4,1)->index();
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
        Schema::dropIfExists('students_daily_memorization');
    }
}
