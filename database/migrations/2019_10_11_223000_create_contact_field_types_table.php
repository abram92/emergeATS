<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactFieldTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_field_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('fontawesome_icon')->nullable();
            $table->string('protocol')->nullable();
            $table->boolean('deletable')->default(1);
            $table->string('type')->nullable();
			$table->date('deleted_at')->nullable();
            $table->timestamps();

        });

        Schema::create('contact_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contact_field_type_id');
            $table->string('data');
			$table->morphs('contactable');
			$table->date('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('contact_field_type_id')->references('id')->on('contact_field_types')->onDelete('cascade');
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
			$table->morphs('addressable');
			$table->date('deleted_at')->nullable();
            $table->timestamps();

        });


        $id = DB::table('contact_field_types')->insertGetId([
            'name' => 'Email',
            'fontawesome_icon' => 'fa fa-envelope',
            'protocol' => 'mailto:',
            'deletable' => false,
            'type' => 'email',
        ]);

        $id = DB::table('contact_field_types')->insertGetId([
            'name' => 'Phone',
            'fontawesome_icon' => 'fa fa-phone-alt',
            'protocol' => 'tel:',
            'deletable' => false,
            'type' => 'phone',
        ]);
		
		$id = DB::table('contact_field_types')->insertGetId([
            'name' => 'Cell',
            'fontawesome_icon' => 'fa fa-mobile-alt',
            'protocol' => 'tel:',
            'deletable' => false,
            'type' => 'phone',
        ]);


        $id = DB::table('contact_field_types')->insertGetId([
            'name' => 'Linkedin',
            'fontawesome_icon' => 'fab fa-linkedin-in',
        ]);
		
		$id = DB::table('contact_field_types')->insertGetId([
            'name' => 'Website',
            'fontawesome_icon' => 'fa fa-globe',
            'protocol' => 'http:',
            'deletable' => false,
            'type' => 'url',
        ]);

		$id = DB::table('contact_field_types')->insertGetId([
            'name' => 'Fax',
            'fontawesome_icon' => 'fa fa-fax',
            'protocol' => '',
            'deletable' => false,
            'type' => 'phone',
        ]);
		
    }
}
