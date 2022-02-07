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
            $table->date('complaint_date')->index();
            $table->foreignId('category_complaint_id')->index()->references('id')->on('complaint_box_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->longText('subject');
            $table->foreignUuid('sender_id')->index()->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('receiver_id')->index()->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('receiver_role_id')->index()->references('id')->on('complaint_box_roles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->longText('reply')->nullable();
            $table->timestamp('read_at')->nullable();
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
        Schema::dropIfExists('box_complaint_suggestions');
    }
}
