<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuranSurasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quran_suras', function (Blueprint $table) {
            $table->id();
            $table->string('name',20)->index()->unique();
            $table->foreignId('quran_part_id')->index()->references('id')->on('quran_parts')->restrictOnDelete();
            $table->unsignedSmallInteger('total_number_aya')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quran_suras');
    }
}
