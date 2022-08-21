<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_exams', function (Blueprint $table) {
            $table->foreignUuid('id')->unique()->index()->references('id')->on('exams')->cascadeOnDelete();
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
        Schema::dropIfExists('external_exams');
    }
}
