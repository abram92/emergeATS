<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_ads', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('jobref');
			$table->unsignedInteger('client_id');
			$table->unsignedInteger('consultant_id');
//			$table->string('jobdescription')->nullable();
			$table->date('start_date')->nullable();
			$table->string('duration')->nullable();
			$table->unsignedInteger('jobtype_id')->nullable();
			$table->unsignedInteger('jobtitle_id')->nullable();
			$table->string('jobtitle_text')->nullable;
			$table->unsignedInteger('salary_category_id')->nullable();
			$table->decimal('salary_from',12,2)->nullable();
			$table->decimal('salary_to',12,2)->nullable();
			$table->decimal('rate_per_hour',12,2)->nullable();
			$table->decimal('rate_per_day',12,2)->nullable();
			
			$table->unsignedInteger('ee_status_id')->nullable();
			$table->unsignedInteger('status_id')->nullable();
			$table->date('activated_at')->nullable();
			$table->date('deleted_at')->nullable();
			
            $table->timestamps();
			
			$table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
			$table->foreign('consultant_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('jobtype_id')->references('id')->on('job_types')->onDelete('cascade');
			$table->foreign('jobtitle_id')->references('id')->on('job_titles')->onDelete('cascade');
			$table->foreign('salary_category_id')->references('id')->on('salary_categories')->onDelete('cascade');
			$table->foreign('ee_status_id')->references('id')->on('ee_statuses')->onDelete('cascade');
			$table->foreign('status_id')->references('id')->on('job_statuses')->onDelete('cascade');
 
        });
		
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_contact_job_ad');
        Schema::dropIfExists('client_contacts');
        Schema::dropIfExists('job_ads');
    }
}
