<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('description');
			$table->string('colour_hex',8)->nullable();
			$table->date('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('team_leader', function (Blueprint $table) {
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('user_id');
			
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['team_id', 'user_id', ], 'team_leader_index');
 
        });		

        Schema::create('team_member', function (Blueprint $table) {
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('user_id');
			
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['team_id', 'user_id', ], 'team_member_index');
 
        });		

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		
        Schema::dropIfExists('team_member');
        Schema::dropIfExists('team_leader');
        Schema::dropIfExists('teams');

    }
}
