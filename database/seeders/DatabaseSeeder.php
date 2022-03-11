<?php

namespace Database\Seeders;

use App\Models\ClientCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CurrencySeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(ClientCategorySeeder::class);
        //$this->call(ClientSeeder::class);
        $this->call(EmployeeCategorySeeder::class);
        $this->call(EmployeeSeeder::class);
    }
}
