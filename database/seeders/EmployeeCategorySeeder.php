<?php

namespace Database\Seeders;

use App\Models\EmployeeCategory;
use Illuminate\Database\Seeder;

class EmployeeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $EmployeeCategory = array(
            array('id' => '1','name' => 'E-books'),
            array('id' => '2','name' => 'Sanitary'),
            array('id' => '3','name' => 'Paints'),
            array('id' => '4','name' => 'Crockery'),
        );


        EmployeeCategory::insert($EmployeeCategory);
    }
}
