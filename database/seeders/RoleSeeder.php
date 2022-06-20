<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ClientCategory = array(
            array('id' => '1','name' => 'super admin','role_key' => 'superadmin'),
            array('id' => '2','name' => 'admin','role_key' => 'admin'),
        );


        Role::insert($ClientCategory);
    }
}
