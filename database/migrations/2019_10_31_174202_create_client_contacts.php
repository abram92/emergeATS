<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
			$table->unsignedBigInteger('client_id');
			$table->string('position')->nullable();
			$table->date('deleted_at')->nullable();	
            $table->timestamps();
			$table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
		
		
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_contacts');
    }
}
