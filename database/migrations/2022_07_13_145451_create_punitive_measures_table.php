<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePunitiveMeasuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punitive_measures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type',['block','warning'])->index();
            $table->enum('reason',['memorize','did-not-memorize','absence','late'])->index();
            $table->boolean('number_times')->unsigned()->index();
            $table->unsignedFloat('quantity',2,1)->nullable()->unsigned()->index();
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
        Schema::dropIfExists('punitive_measures');
    }
}
