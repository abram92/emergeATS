<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('calendar_events', function(Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('type_id')->nullable();
			$table->unsignedInteger('user_id')->nullable();
			$table->unsignedInteger('created_user_id')->nullable();
            $table->string('title');
            $table->dateTime('time_start');
            $table->dateTime('time_end');
            $table->boolean('is_all_day')->default('f');
			$table->boolean('deletable')->default('t');
            $table->string('background_colour')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();
			
			$table->foreign('type_id')->references('id')->on('event_types')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('created_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'type_id']);			
			
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('calendar_events');
	}

}
