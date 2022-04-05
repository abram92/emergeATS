<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
       $roles = [
           ['Admin','#00CC00'],  // green
           ['Consultant','#0099CC'],  //light blue
           ['Team Lead', '#003399'],   //dark blue
           ['Guest', '#ffffcc'],  //light
		   ['Data Exporter', '#FF9900'],  // orange
		   ['Static Work Admin', '#99FF00'],  // lime
		   ['Bulk Email Candidates', '#9900FF']  // 
           
        ];


        foreach ($roles as $role) {
             Role::create(['name' => $role[0], 'colour_hex'=> $role[1]]);
        }
		
    }
}
