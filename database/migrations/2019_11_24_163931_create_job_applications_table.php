<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedInteger('candidate_id');
			$table->morphs('applicationable');			
			$table->unsignedInteger('status_id')->nullable();
			$table->text('comments')->nullable();
			$table->date('deleted_at')->nullable();
			
            $table->timestamps();
			
			$table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
			$table->foreign('status_id')->references('id')->on('job_application_statuses')->onDelete('cascade');
 
        });
		
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_applications');
    }
}
