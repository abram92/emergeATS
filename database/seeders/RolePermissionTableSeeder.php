<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		$roles = [
           'Admin' => 'data-search', 
           'Consultant' => 'data-search', 
           'Team Lead' => 'data-search', 
 		   'Data Exporter' => 'data-export'
          
        ];


        foreach ($roles as $role => $permission) {
			$roleRec = Role::whereName($role)->first();
              $roleRec->givePermissionTo($permission);
        }


    }
}
