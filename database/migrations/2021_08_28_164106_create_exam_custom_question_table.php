<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamCustomQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_custom_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_part_id')->unique()->index()->references('id')->on('quran_parts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('exam_question_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_custom_questions');
    }
}
