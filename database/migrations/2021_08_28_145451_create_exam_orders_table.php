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
            $table->enum('status',['in-pending','rejected','acceptable','failure'])->default('in-pending')->index();
            $table->enum('type',['new','improvement'])->default('new')->index();
            $table->foreignId('user_signature_id')->index()->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('partable_id')->index();
            $table->string('partable_type',30)->index();
            $table->foreignId('student_id')->index()->references('id')->on('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->cascadeOnDelete();
            $table->foreignId('tester_id')->nullable()->index()->references('id')->on('testers')->cascadeOnDelete();
            $table->dateTime('datetime')->nullable()->index();
            $table->enum('suggested_day',['saturday','sunday','monday','tuesday','wednesday','thursday','friday'])->index();
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
        Schema::dropIfExists('exam_orders');
    }
}
