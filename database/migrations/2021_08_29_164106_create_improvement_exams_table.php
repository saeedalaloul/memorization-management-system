<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImprovementExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('improvement_exams', function (Blueprint $table) {
            $table->foreignUuid('id')->unique()->index()->references('id')->on('exams')->cascadeOnDelete();
            $table->foreignId('tester_id')->index()->references('id')->on('testers')->restrictOnDelete();
            $table->boolean('mark')->unsigned()->index();
            $table->dateTime('datetime')->index();
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
        Schema::dropIfExists('improvement_exams');
    }
}
