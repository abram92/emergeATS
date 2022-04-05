<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliases', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('description');
			$table->integer('minimum_parser_matches')->nullable();
            $table->unsignedInteger('alias_category_id')->nullable();
			$table->date('deleted_at')->nullable();
            $table->timestamps();
			
			$table->foreign('alias_category_id')->references('id')->on('alias_categories')->onDelete('cascade');
        });


        Schema::create('alias_keywords', function (Blueprint $table) {
            $table->bigInteger('alias_id');
			$table->string('keyword');
            $table->timestamps();
			$table->unique(['alias_id', 'keyword']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aliases');
    }
}
