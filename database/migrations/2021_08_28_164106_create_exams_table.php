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
            $table->json('readable')->index();
            $table->json('signs_questions')->index();
            $table->json('marks_questions')->index();
            $table->unsignedTinyInteger('another_mark')->index();
            $table->foreignId('quran_part_id')->index()->references('id')->on('quran_parts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('exam_success_mark_id')->index()->references('id')->on('exam_success_mark')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('student_id')->index()->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('tester_id')->nullable()->index()->references('id')->on('testers')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('exams');
    }
}
