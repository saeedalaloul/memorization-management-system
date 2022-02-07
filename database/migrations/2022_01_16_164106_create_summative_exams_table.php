<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummativeExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summative_exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('readable');
            $table->json('signs_questions');
            $table->json('marks_questions');
            $table->unsignedTinyInteger('another_mark')->index();
            $table->foreignId('quran_summative_part_id')->index()->references('id')->on('quran_summative_parts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('exam_summative_success_mark_id')->index()->references('id')->on('exam_summative_success_mark')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('student_id')->index()->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('tester_id')->nullable()->index()->references('id')->on('testers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('exam_date')->index();
            $table->string('notes', 50)->nullable();
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
        Schema::dropIfExists('summative_exams');
    }
}
