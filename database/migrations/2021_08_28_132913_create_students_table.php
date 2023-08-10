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
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->foreignId('id')->unique()->index()->references('id')->on('users')->restrictOnDelete();
            $table->foreignId('father_id')->index()->references('id')->on('fathers')->restrictOnDelete();
            $table->foreignUuid('grade_id')->index()->references('id')->on('grades')->restrictOnDelete();
            $table->foreignUuid('group_id')->index()->references('id')->on('groups')->restrictOnDelete();
            $table->foreignUuid('group_sunnah_id')->nullable()->index()->references('id')->on('groups')->restrictOnDelete();
            $table->foreignId('current_part_id')->nullable()->references('id')->on('quran_parts')->restrictOnDelete();
            $table->foreignId('current_part_cumulative_id')->nullable()->references('id')->on('quran_parts')->restrictOnDelete();
            $table->boolean('current_revision_count')->default(1);
            $table->boolean('current_cumulative_revision_count')->default(1);
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
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
}
