<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->bigInteger('id');
			$table->date('birthdate')->nullable();
			$table->string('idnumber')->nullable();
			$table->bigInteger('consultant_id');
			$table->boolean('duplicate')->default(false);
			$table->bigInteger('current_location_id')->nullable();
			$table->bigInteger('jobtitle_id')->nullable();
			$table->string('jobtitle_text')->nullable();
			$table->decimal('salary',15,2)->nullable();
			$table->string('job_ref_codes')->nullable();
			$table->string('job_ref_codes_max')->nullable();
			$table->bigInteger('candidate_level_id')->nullable();
			$table->bigInteger('candidate_rating_id')->nullable();
			$table->bigInteger('salary_category_id')->nullable();
			$table->bigInteger('ee_status_id')->nullable();
			$table->bigInteger('status_id');
			$table->bigInteger('availability_id')->nullable();
			$table->boolean('interviewed')->default(false);
			$table->date('activated_at')->nullable();
			$table->date('deleted_at')->nullable();
            $table->timestamps();
			
			$table->primary('id');
			$table->foreign('id')->references('id')->on('users');
			$table->foreign('consultant_id')->references('id')->on('users');
			$table->foreign('current_location_id')->references('id')->on('locations');
			$table->foreign('jobtitle_id')->references('id')->on('job_titles');
			$table->foreign('candidate_level_id')->references('id')->on('candidate_levels');
			$table->foreign('candidate_rating_id')->references('id')->on('candidate_ratings');
			$table->foreign('salary_category_id')->references('id')->on('salary_categories');
			$table->foreign('ee_status_id')->references('id')->on('ee_statuses');
			$table->foreign('status_id')->references('id')->on('candidate_statuses');
			$table->foreign('availability_id')->references('id')->on('candidate_availabilities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidates');
    }
}
