<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentReportsStatusesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_reports_statuses', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('student_id')->unique()->index()->references('id')->on('students')->restrictOnDelete();
            $table->enum('status',['send_failure','ready_to_send'])->index();
            $table->json('details');
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
        Schema::dropIfExists('student_reports_statuses');
    }
}
