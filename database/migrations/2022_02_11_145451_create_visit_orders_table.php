<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('hostable_type');
            $table->char('hostable_id',36);
            $table->dateTime('datetime')->index();
            $table->enum('status',['in-pending','in-sending','in-approval'])->default('in-pending')->index();
            $table->foreignId('oversight_member_id')->index()->references('id')->on('oversight_members')->restrictOnDelete();
            $table->mediumText('notes')->nullable();
            $table->mediumText('suggestions')->nullable();
            $table->mediumText('recommendations')->nullable();
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
