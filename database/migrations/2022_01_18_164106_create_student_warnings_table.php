<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_warnings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->index()->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('warning_expiry_date')->nullable()->index();
            $table->string('notes', 50)->nullable();
            $table->json('readable');
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
        Schema::dropIfExists('student_warnings');
    }
}
