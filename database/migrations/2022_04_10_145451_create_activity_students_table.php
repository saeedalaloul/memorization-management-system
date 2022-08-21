<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_students', function (Blueprint $table) {
            $table->foreignUuid('activity_id')->index()->references('id')->on('activities')->restrictOnDelete();
            $table->foreignId('student_id')->index()->references('id')->on('students')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_students');
    }
}
