<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('description');
			$table->string('colour_hex',8)->nullable();
			$table->unsignedInteger('sort_seq')->nullable();
			$table->date('deleted_at')->nullable();
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
        Schema::dropIfExists('salary_categories');
    }
}
