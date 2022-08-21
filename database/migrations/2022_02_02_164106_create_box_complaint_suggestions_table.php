<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxComplaintSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('box_complaint_suggestions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('datetime')->index();
            $table->enum('category',['complaint','suggestion','idea'])->index();
            $table->longText('subject');
            $table->foreignId('sender_id')->index()->references('id')->on('users')->restrictOnDelete();
            $table->foreignId('receiver_id')->index()->references('id')->on('users')->restrictOnDelete();
            $table->longText('reply')->nullable();
            $table->timestamp('subject_read_at')->nullable()->index();
            $table->timestamp('reply_read_at')->nullable()->index();
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
        Schema::dropIfExists('box_complaint_suggestions');
    }
}
