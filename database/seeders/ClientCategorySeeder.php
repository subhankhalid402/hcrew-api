<?php

namespace Database\Seeders;

use App\Models\ClientCategory;
use Illuminate\Database\Seeder;

class ClientCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ClientCategory = array(
            array('id' => '1','name' => 'Education Fee',),
            array('id' => '2','name' => 'Cble T.V',),
            array('id' => '3','name' => 'Emi Bill Pay',),
            array('id' => '4','name' => 'Fastag',),
        );


        ClientCategory::insert($ClientCategory);
    }
}
