<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorshipSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsorship_supervisors', function (Blueprint $table) {
            $table->foreignUuid('sponsorship_id')->index()->references('id')->on('sponsorships')->cascadeOnDelete();
            $table->foreignId('sponsorship_supervisor_id')->index()->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sponsorship_supervisors');
    }
}
