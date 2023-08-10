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
            $table->set('suggested_exam_days',['saturday','sunday','monday','tuesday','wednesday','thursday','friday'])->index();
            $table->unsignedTinyInteger('exam_questions_summative_three_part');
            $table->unsignedTinyInteger('exam_questions_summative_five_part');
            $table->unsignedTinyInteger('exam_questions_summative_ten_part');
            $table->unsignedTinyInteger('exam_questions_min');
            $table->unsignedTinyInteger('exam_questions_max');
            $table->unsignedTinyInteger('exam_sunnah_questions_summative');
            $table->unsignedTinyInteger('exam_sunnah_questions');
            $table->unsignedTinyInteger('number_days_exam');
            $table->unsignedTinyInteger('number_days_exam_two_left');
            $table->unsignedTinyInteger('number_days_exam_three_left');
            $table->unsignedTinyInteger('number_days_exam_sunnah');
            $table->unsignedTinyInteger('exam_success_rate');
            $table->unsignedTinyInteger('exam_sunnah_success_rate');
            $table->unsignedTinyInteger('summative_exam_success_rate');
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
        Schema::dropIfExists('exam_settings');
    }
}
