<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Currencies =  [
            [
                'name' => 'USD',
            ],
            [
                'name' => 'EUR',
            ]
        ];

        foreach ($Currencies as $currency){
            Currency::create($currency);
        }
    }
}
