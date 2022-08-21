<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityOrderStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_order_students', function (Blueprint $table) {
            $table->foreignUuid('activity_order_id')->index()->references('id')->on('activity_orders')->cascadeOnDelete();
            $table->foreignId('student_id')->index()->references('id')->on('students')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_order_students');
    }
}
