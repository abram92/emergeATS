<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoggedEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logged_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->string('address_from')->nullable();
            $table->string('address_to')->nullable();
            $table->string('address_cc')->nullable();
            $table->string('address_bcc')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->text('headers')->nullable();
            $table->string('messageId', 32)->nullable()->default(null);
            $table->string('mail_driver', 255)->nullable()->default(null);
            $table->index('messageId');
            $table->index('address_to');
            $table->index('subject');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logged_emails');
    }
}
