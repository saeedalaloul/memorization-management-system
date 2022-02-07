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
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->index()->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('grade_id')->index()->references('id')->on('grades')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('group_id')->index()->references('id')->on('groups')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('attendance_date')->index();
            $table->boolean('attendance_status')->index();
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
