<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticWorkEmailCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_work_email_candidates', function (Blueprint $table) {
            $table->unsignedInteger('email_id');
            $table->unsignedInteger('candidate_id');
            $table->unsignedInteger('status_id');
			$table->boolean('reminder')->default(false);
			
			$table->foreign('email_id')->references('id')->on('static_work_emails')->onDelete('cascade');
			$table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
			$table->foreign('status_id')->references('id')->on('candidate_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('static_work_email_candidates');
    }
}
