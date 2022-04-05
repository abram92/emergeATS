<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_holidays', function (Blueprint $table) {
            $table->bigIncrements('id');
 			$table->date('holiday_date');
            $table->boolean('recurring');
			$table->string('description');
            $table->timestamps();

        });
		
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'New Year\'s Day',
            'holiday_date' => '2020-01-01',
            'recurring' => 'true'
        ]);

        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Human Rights Day',
            'holiday_date' => '2020-03-21',
            'recurring' => 'true'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Good Friday',
            'holiday_date' => '2020-04-10',
            'recurring' => 'false'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Family Day',
            'holiday_date' => '2020-04-13',
            'recurring' => 'false'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Freedom Day',
            'holiday_date' => '2020-04-27',
            'recurring' => 'true'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Workers\' Day',
            'holiday_date' => '2020-05-01',
            'recurring' => 'true'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Youth Day',
            'holiday_date' => '2020-06-16',
            'recurring' => 'true'
        ]);

        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'National Womens\' Day',
            'holiday_date' => '2020-08-09',
            'recurring' => 'true'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Public Holiday',
            'holiday_date' => '2020-08-10',
            'recurring' => 'false'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Heritage Day',
            'holiday_date' => '2020-09-24',
            'recurring' => 'true'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Day of Reconciliation',
            'holiday_date' => '2020-12-16',
            'recurring' => 'true'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Christmas Day',
            'holiday_date' => '2020-12-25',
            'recurring' => 'true'
        ]);
        $id = DB::table('public_holidays')->insertGetId([
            'description' => 'Day of Goodwill',
            'holiday_date' => '2020-12-26',
            'recurring' => 'true'
        ]);
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_holidays');
    }
}
