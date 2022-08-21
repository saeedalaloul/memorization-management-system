<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('datetime')->index();
            $table->foreignId('activity_member_id')->nullable()->index()->references('id')->on('activity_members')->restrictOnDelete();
            $table->foreignId('activity_type_id')->index()->references('id')->on('activity_types')->restrictOnDelete();
            $table->foreignId('teacher_id')->index()->references('id')->on('teachers')->restrictOnDelete();
            $table->string('notes', 50)->nullable();
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
        Schema::dropIfExists('activities');
    }
}
