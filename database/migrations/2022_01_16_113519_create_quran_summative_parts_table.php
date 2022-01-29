<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuranSummativePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quran_summative_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name',20)->index();
            $table->boolean('number_parts')->index();
            $table->string('description',35)->index()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quran_summative_parts');
    }
}
