<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSunnahPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sunnah_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name',15)->index()->unique();
            $table->boolean('total_hadith_parts')->unsigned()->index();
            $table->foreignId('sunnah_book_id')->index()->references('id')->on('sunnah_books')->restrictOnDelete();
            $table->boolean('arrangement')->unsigned()->index()->unique();
            $table->enum('type',['individual','deserved'])->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sunnah_parts');
    }
}
