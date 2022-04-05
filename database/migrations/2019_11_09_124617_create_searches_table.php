<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('description')->nullable();
            $table->string('search_type');
            $table->json('parameters');
            $table->json('filtercriteria');
            $table->date('saved_at')->nullable();
			
            $table->timestamps();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');			
            $table->index(['user_id', 'search_type']);
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('searches');
    }
}
