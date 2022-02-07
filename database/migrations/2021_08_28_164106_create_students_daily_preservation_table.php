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
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->index()->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('type')->index()->references('id')->on('daily_preservation_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('from_sura')->index()->references('id')->on('quran_suras')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('to_sura')->index()->references('id')->on('quran_suras')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedSmallInteger('from_aya')->index();
            $table->unsignedSmallInteger('to_aya')->index();
            $table->foreignId('evaluation')->index()->references('id')->on('daily_preservation_evaluations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('daily_preservation_date')->index();
            $table->timestamps();
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
