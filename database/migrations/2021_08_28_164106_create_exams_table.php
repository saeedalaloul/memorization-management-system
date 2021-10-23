<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->json('readable');
            $table->json('signs_questions');
            $table->json('marks_questions');
            $table->foreignId('quran_part_id')->index()->references('id')->on('quran_parts')->cascadeOnDelete();
            $table->foreignId('exam_success_mark_id')->index()->references('id')->on('exam_success_mark')->cascadeOnDelete();
            $table->foreignId('student_id')->index()->references('id')->on('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete();
            $table->foreignId('tester_id')->nullable()->index()->references('id')->on('testers')->cascadeOnDelete();
            $table->date('exam_date')->index();
            $table->string('notes',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
