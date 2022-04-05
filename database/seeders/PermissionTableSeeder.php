<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'adminfunc-list',
           'adminfunc-create',
           'adminfunc-edit',
           'adminfunc-delete',
		   'candidate-list',
           'candidate-create',
           'candidate-edit',
           'candidate-delete',
		   'client-list',
           'client-create',
           'client-edit',
           'client-delete',
		   'job-list',
           'job-create',
           'job-edit',
           'job-delete',
		   'job-publish',
           'data-export',
           'team-view',
           'guest-view',
		   'data-search'
           
        ];


        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
