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
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('password');
            $table->string('current_role',15)->nullable();
            $table->date('dob');
            $table->string('phone',10)->unique();
            $table->string('identification_number',9)->unique();
            $table->rememberToken();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('address',100);
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
        Schema::dropIfExists('users');
    }
}
