<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('allow_exams_update');
            $table->unsignedTinyInteger('exam_questions_summative_three_part');
            $table->unsignedTinyInteger('exam_questions_summative_five_part');
            $table->unsignedTinyInteger('exam_questions_summative_ten_part');
            $table->unsignedTinyInteger('exam_questions_summative_fifteen_part');
            $table->unsignedTinyInteger('exam_questions_min');
            $table->unsignedTinyInteger('exam_questions_max');
            $table->unsignedTinyInteger('number_days_exam');
            $table->unsignedTinyInteger('exam_success_rate');
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
        Schema::dropIfExists('exam_settings');
    }
}
