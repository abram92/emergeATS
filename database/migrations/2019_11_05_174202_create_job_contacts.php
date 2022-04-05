<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		
        Schema::create('client_contact_job_ad', function (Blueprint $table) {
            $table->unsignedInteger('client_contact_id');
            $table->unsignedInteger('job_ad_id');
			
			$table->foreign('client_contact_id')->references('id')->on('client_contacts')->onDelete('cascade');
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
        Schema::dropIfExists('client_contact_job_ad');
    }
}
