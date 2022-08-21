<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuranPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quran_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name',10)->index()->unique();
            $table->boolean('arrangement')->unsigned()->index()->unique();
            $table->boolean('total_preservation_parts')->unsigned()->index();
            $table->enum('type',['individual','deserved'])->index();
            $table->string('description',20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quran_parts');
    }
}
