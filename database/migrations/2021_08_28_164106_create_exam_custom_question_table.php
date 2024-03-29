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
            $table->uuid('id')->primary();
            $table->foreignId('quran_part_id')->unique()->index()->references('id')->on('quran_parts')->cascadeOnDelete();
            $table->unsignedTinyInteger('question_count')->index();
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
        Schema::dropIfExists('exam_custom_questions');
    }
}
