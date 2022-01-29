<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLowerSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lower_supervisors', function (Blueprint $table) {
            $table->foreignId('id')->unique()->index()->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('grade_id')->index()->references('id')->on('grades')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lower_supervisors');
    }
}
