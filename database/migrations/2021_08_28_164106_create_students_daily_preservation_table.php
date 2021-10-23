<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsDailyPreservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_daily_preservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index()->references('id')->on('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete();
            $table->foreignId('type')->index()->references('id')->on('daily_preservation_types')->cascadeOnDelete();
            $table->foreignId('from_sura')->index()->references('id')->on('quran_suras')->cascadeOnDelete();
            $table->foreignId('to_sura')->index()->references('id')->on('quran_suras')->cascadeOnDelete();
            $table->unsignedTinyInteger('from_aya')->index();
            $table->unsignedTinyInteger('to_aya')->index();
            $table->foreignId('evaluation')->index()->references('id')->on('daily_preservation_evaluations')->cascadeOnDelete();
            $table->date('daily_preservation_date')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_daily_preservations');
    }
}
