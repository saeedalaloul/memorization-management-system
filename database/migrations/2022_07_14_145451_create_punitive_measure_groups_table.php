<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePunitiveMeasureGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punitive_measure_groups', function (Blueprint $table) {
            $table->foreignUuid('punitive_measure_id')->index()->references('id')->on('punitive_measures')->cascadeOnDelete();
            $table->foreignUuid('group_id')->index()->references('id')->on('groups')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('punitive_measure_groups');
    }
}
