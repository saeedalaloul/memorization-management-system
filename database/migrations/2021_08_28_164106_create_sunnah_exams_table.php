<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSunnahExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sunnah_exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('mark')->unsigned()->index();
            $table->foreignId('sunnah_part_id')->index()->references('id')->on('sunnah_parts')->restrictOnDelete();
            $table->foreignId('exam_success_mark_id')->index()->references('id')->on('exam_success_mark')->restrictOnDelete();
            $table->foreignId('student_id')->index()->references('id')->on('students')->restrictOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->restrictOnDelete();
            $table->foreignId('tester_id')->index()->references('id')->on('testers')->restrictOnDelete();
            $table->dateTime('datetime')->index();
            $table->string('notes', 50)->nullable();
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
        Schema::dropIfExists('sunnah_exams');
    }
}
