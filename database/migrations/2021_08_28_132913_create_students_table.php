<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->foreignId('id')->unique()->index()->references('id')->on('users')->restrictOnDelete();
            $table->foreignId('father_id')->index()->references('id')->on('fathers')->restrictOnDelete();
            $table->foreignUuid('grade_id')->index()->references('id')->on('grades')->restrictOnDelete();
            $table->foreignUuid('group_id')->index()->references('id')->on('groups')->restrictOnDelete();
            $table->string('whatsapp_number',13)->index();
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
        Schema::dropIfExists('students');
    }
}
