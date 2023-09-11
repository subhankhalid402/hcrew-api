<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Setting = array(
            array('id' => '1','company_name' => 'WHITE HORIZON','email' => 'info@whitehorizoncrew.com','website' => 'http://www.whitehorizoncrew.com/','logo' => 'logo.png','signature' => '4lhEyOJlAg.png','phone_no' => '+97150 24 22 125','phone_no_2' => '','address' => 'P.O.Box No.43718 Dubai,(U.A.E)', 'bank_name' => 'Abu Dhabi Commercial Bank (ADCB)', 'account' => 'White Horizon Technical Works LLC', 'swift_code' => 'ADCBAEAA060','account_no' => '1148 6267 8200 01', 'iban_number' => 'AE 9600 3001 1486 2678 2000 1', 'tax_number' => '100214524900003' ,'created_at' => NULL,'updated_at' => '2022-10-13 05:48:42')
        );


        Setting::insert($Setting);
    }
}
