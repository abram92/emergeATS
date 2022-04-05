<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatePreferredLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		
        Schema::create('candidate_preferred_location', function (Blueprint $table) {
            $table->unsignedInteger('candidate_id');
            $table->unsignedInteger('location_id');
			
			$table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
			$table->unique(['candidate_id', 'location_id']);
 
        });		
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_preferred_location');
    }
}
