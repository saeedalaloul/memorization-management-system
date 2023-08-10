<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSunnahExternalExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sunnah_external_exams', function (Blueprint $table) {
            $table->foreignUuid('id')->unique()->index()->references('id')->on('sunnah_exams')->cascadeOnDelete();
            $table->boolean('mark')->unsigned()->index();
            $table->date('date')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_sunnah_exams');
    }
}
