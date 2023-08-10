<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackStudentTransfersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('track_student_transfers', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('student_id')->index()->references('id')->on('students')->cascadeOnDelete();
            $table->foreignUuid('old_grade_id')->index()->references('id')->on('grades')->cascadeOnDelete();
            $table->foreignId('old_teacher_id')->nullable()->index()->references('id')->on('teachers')->cascadeOnDelete();
            $table->foreignUuid('new_grade_id')->index()->references('id')->on('grades')->cascadeOnDelete();
            $table->foreignId('new_teacher_id')->nullable()->index()->references('id')->on('teachers')->cascadeOnDelete();
            $table->foreignId('user_signature_id')->index()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('user_signature_role_id')->index()->references('id')->on('roles')->cascadeOnDelete();
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
        Schema::dropIfExists('track_student_transfers');
    }
}
