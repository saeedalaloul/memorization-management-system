<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorshipGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsorship_groups', function (Blueprint $table) {
            $table->foreignUuid('sponsorship_id')->index()->references('id')->on('sponsorships')->cascadeOnDelete();
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
        Schema::dropIfExists('sponsorship_groups');
    }
}
