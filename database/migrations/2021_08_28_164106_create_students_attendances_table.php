<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index()->references('id')->on('students')->cascadeOnDelete();
            $table->foreignId('grade_id')->index()->references('id')->on('grades')->cascadeOnDelete();
            $table->foreignId('group_id')->index()->references('id')->on('groups')->cascadeOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete();
            $table->date('attendance_date')->index();
            $table->boolean('attendance_status');
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
        Schema::dropIfExists('student_attendances');
    }
}
