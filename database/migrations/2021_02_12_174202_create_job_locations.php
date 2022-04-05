<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		
        Schema::create('job_ad_location', function (Blueprint $table) {
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('job_ad_id');
			
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
			$table->foreign('job_ad_id')->references('id')->on('job_ads')->onDelete('cascade');
 
        });		
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_ad_location');
    }
}
