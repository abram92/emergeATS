<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticWorkAlertJobAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_work_alert_job_ads', function (Blueprint $table) {
            $table->unsignedInteger('alert_id');
            $table->unsignedInteger('job_ad_id');
            $table->unsignedInteger('alert_level');
            $table->unsignedInteger('level_ordinal');
            $table->unsignedInteger('status_id');
			
			$table->foreign('alert_id')->references('id')->on('static_work_alerts')->onDelete('cascade');
			$table->foreign('job_ad_id')->references('id')->on('job_ads')->onDelete('cascade');
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
        Schema::dropIfExists('static_work_alert_job_ads');
    }
}
