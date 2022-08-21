<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->foreignId('id')->unique()->index()->references('id')->on('users')->cascadeOnDelete();
            $table->enum('economic_situation',['good','moderate','difficult'])->index();
            $table->enum('recitation_level',['al-qaida-al-nooraniah','qualifying','high','tahil-alsanad','sanad'])->default('al-qaida-al-nooraniah')->index();
            $table->string('academic_qualification',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_infos');
    }
}
