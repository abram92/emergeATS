<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('name');
			$table->unsignedInteger('status_id');
 			$table->unsignedInteger('agencynotes_id')->nullable();
			$table->unsignedInteger('techenvironment_id')->nullable();
			$table->boolean('prospect')->nullable();
			$table->unsignedInteger('consultant_id');
			$table->date('deleted_at')->nullable();
 
            $table->foreign('status_id')->references('id')->on('client_statuses')->onDelete('cascade');
            $table->foreign('agencynotes_id')->references('id')->on('long_full_texts')->onDelete('cascade');
            $table->foreign('techenvironment_id')->references('id')->on('long_full_texts')->onDelete('cascade');
            $table->foreign('consultant_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
