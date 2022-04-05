<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarEventEntitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('calendar_event_entities', function(Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('event_id')->nullable();
			$table->morphs('entityable');
            $table->timestamps();
			
			$table->foreign('event_id')->references('id')->on('calendar_events')->onDelete('cascade');
			
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('calendar_event_entities');
	}

}
