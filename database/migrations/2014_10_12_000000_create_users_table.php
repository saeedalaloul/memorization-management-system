<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->unique();
            $table->string('email')->index()->nullable()->unique();
            $table->timestamp('email_verified_at')->index()->nullable();
            $table->timestamp('last_seen')->index()->nullable();
            $table->boolean('status')->default(1);
            $table->string('password')->nullable();
            $table->string('current_role',15)->nullable();
            $table->date('dob')->nullable();
            $table->string('phone',10)->index()->nullable()->unique();
            $table->string('identification_number',9)->index()->unique();
            $table->rememberToken();
            $table->string('profile_photo', 2048)->nullable();
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
        Schema::dropIfExists('users');
    }
}
