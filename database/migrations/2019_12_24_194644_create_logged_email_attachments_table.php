<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoggedEmailAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logged_email_attachments', function (Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('email_id');
            $table->string('filename');
            $table->string('filetype');
            $table->integer('size');
            $table->string('host');
            $table->string('location');
			$table->foreign('email_id')->references('id')->on('logged_emails')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logged_email_attachments');
    }
}
