<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->tinyInteger('status')->default(0)->index();
            $table->json('readable');
            $table->foreignId('quran_part_id')->index()->references('id')->on('quran_parts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('student_id')->unique()->index()->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('tester_id')->nullable()->index()->references('id')->on('testers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('exam_date')->nullable()->index();
            $table->string('notes', 50)->nullable();
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
        Schema::dropIfExists('exam_orders');
    }
}
