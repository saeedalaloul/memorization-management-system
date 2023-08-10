<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('hostable_type',30)->index();
            $table->unsignedBigInteger('hostable_id')->index();
            $table->dateTime('datetime')->index();
            $table->enum('status',['in-pending','replied','in-process','failure','solved'])->default('in-pending')->index();
            $table->foreignId('oversight_member_id')->index()->references('id')->on('oversight_members')->restrictOnDelete();
            $table->mediumText('notes');
            $table->mediumText('suggestions');
            $table->mediumText('recommendations');
            $table->mediumText('reply')->nullable();
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
        Schema::dropIfExists('visit_orders');
    }
}
