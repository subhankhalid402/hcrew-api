<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = array(
            array(
                'id' => '1',
                'first_name' => 'Kenneth S',
                'last_name' => 'Franklin',
                'email' => 'KennethSFranklin@rhyta.com',
                'phone_no' => '773-473-0787',
                'address' => '3451 Cherry Camp Road',
                'passport_number' => '351-78',
                'employee_category_id' => '1',
                'bio' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.1 Safari/605.1.15',
                'picture' => 'EblkJVCodD.png',
                'hours_in_day' => '8',
                'rate_per_day' => '900'
            ),
            array(
                'id' => '2',
                'first_name' => 'Albert D',
                'last_name' => 'Cochran',
                'email' => 'AlbertDCochran@rhyta.com',
                'phone_no' => '319-849-8546',
                'address' => 'Center Point, IA 52213',
                'passport_number' => '478-04',
                'employee_category_id' => '2',
                'bio' => 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
                'picture' => 'CpBDg2YTev.jpg',
                'hours_in_day' => '8',
                'rate_per_day' => '900'
            ),
            array(
                'id' => '3',
                'first_name' => 'Hugh V',
                'last_name' => 'Moody',
                'email' => 'HughVMoody@teleworm.us',
                'phone_no' => '06301 96 70 14',
                'address' => 'Grosse Praesidenten Str. 17',
                'passport_number' => '918-09',
                'employee_category_id' => '3',
                'bio' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',
                'picture' => 'tnlwbQbrIM.png',
                'hours_in_day' => '8',
                'rate_per_day' => '900'
            ),
            array(
                'id' => '4',
                'first_name' => 'Edmond C',
                'last_name' => 'Gauthier',
                'email' => 'EdmondCGauthier@dayrep.com',
                'phone_no' => '(026) 9831-706',
                'address' => '42 Mountfield Terrace',
                'passport_number' => '315-02',
                'employee_category_id' => '4',
                'bio' => 'Mozilla/5.0 (X11; Linux x86_64)',
                'picture' => 'V8IxTOGuQq.jpg',
                'hours_in_day' => '8',
                'rate_per_day' => '900'
            ),
            array(
                'id' => '5',
                'first_name' => 'Corey D',
                'last_name' => 'Lewis',
                'email' => 'CoreyDLewis@armyspy.com',
                'phone_no' => '05944 33 15 31',
                'address' => 'Lietzenburger Straße 22',
                'passport_number' => '819-65',
                'employee_category_id' => '2',
                'bio' => 'Mozilla/5.0 (X11; Linux x86_64)',
                'picture' => 'XbG4GhvSoJ.jpg',
                'hours_in_day' => '8',
                'rate_per_day' => '900'
            ),
            array(
                'id' => '6',
                'first_name' => 'Alice C',
                'last_name' => 'Jones',
                'email' => 'AliceCJones@jourrapide.com',
                'phone_no' => '(026) 7785-282',
                'address' => '210 Haineswood Lane Avonhead Christchurch 8042',
                'passport_number' => '129-06',
                'employee_category_id' => '4',
                'bio' => 'Mozilla/5.0 (X11; Linux x86_64)',
                'picture' => 'yfaGJrrijM.png',
                'hours_in_day' => '8',
                'rate_per_day' => '900'
            ),
        );


        Employee::insert($clients);
    }
}
