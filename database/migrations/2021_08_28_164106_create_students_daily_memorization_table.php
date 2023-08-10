<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsDailyMemorizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('students_daily_memorization', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('student_id')->index()->references('id')->on('students')->restrictOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->restrictOnDelete();
            $table->enum('type',['memorize','review','cumulative-review'])->index();
            $table->enum('cumulative_type',[1,3,5,10,15,20,25,30])->default(1)->index();
            $table->unsignedDecimal('number_pages',4,1)->index();
            $table->enum('evaluation',['excellent','very-good','good','weak'])->index();
            $table->boolean('count_review')->default(1)->index();
            $table->dateTime('datetime')->index();
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('students_daily_memorization');
    }
}
