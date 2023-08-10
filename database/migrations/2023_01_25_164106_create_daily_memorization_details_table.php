<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyMemorizationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('daily_memorization_details', function (Blueprint $table) {
            $table->foreignUuid('id')->index()->references('id')->on('students_daily_memorization');
            $table->foreignId('sura_id')->index()->references('id')->on('quran_suras')->restrictOnDelete();
            $table->unsignedSmallInteger('aya_from')->index();
            $table->unsignedSmallInteger('aya_to')->index();
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
        Schema::dropIfExists('daily_memorization_details');
    }
}
