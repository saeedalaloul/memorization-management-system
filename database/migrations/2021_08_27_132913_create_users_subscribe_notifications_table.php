<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersSubscribeNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscribe_notifications', function (Blueprint $table) {
            $table->foreignId('id')->unique()->index()->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('player_id', 40)->unique()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscribe_notifications');
    }
}
