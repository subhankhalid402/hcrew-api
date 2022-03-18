<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
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
                'name' => 'Kenneth S',
                'short_name' => 'Franklin',
                'email' => 'KennethSFranklin@rhyta.com',
                'phone_no' => '773-473-0787',
                'address' => '3451 Cherry Camp Road',
                'tax_number' => '351-78',
                'client_category_id' => '1',
                'notes' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.1 Safari/605.1.15',
                'logo' => 'EblkJVCodD.png',
                'currency_id' => 1,
                'focal_name' => 'GEX',
                'focal_phone_no' => '773-473-0787',
                'focal_email' => 'Eim6aiS0an@gmail.com',
                'website' => 'jattmobi.com'
            ),
            array(
                'id' => '2',
                'name' => 'Albert D',
                'short_name' => 'Cochran',
                'email' => 'AlbertDCochran@rhyta.com',
                'phone_no' => '319-849-8546',
                'address' => 'Center Point, IA 52213',
                'tax_number' => '478-04',
                'client_category_id' => '2',
                'notes' => 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
                'logo' => 'CpBDg2YTev.jpg',
                'currency_id' => 2,
                'focal_name' => 'Techo',
                'focal_phone_no' => '532-034-6014',
                'focal_email' => 'Chare1996@gmail.com',
                'website' => 'firesyndicatemc.com'
            ),
            array(
                'id' => '3',
                'name' => 'Hugh V',
                'short_name' => 'Moody',
                'email' => 'HughVMoody@teleworm.us',
                'phone_no' => '06301 96 70 14',
                'address' => 'Grosse Praesidenten Str. 17',
                'tax_number' => '918-09',
                'client_category_id' => '3',
                'notes' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',
                'logo' => 'tnlwbQbrIM.png',
                'currency_id' => 1,
                'focal_name' => 'Mr. Good Buys',
                'focal_phone_no' => '773-034-6014',
                'focal_email' => 'Gooks1985@gmail.com',
                'website' => 'CertifiedLicense.de'
            ),
            array(
                'id' => '4',
                'name' => 'Edmond C',
                'short_name' => 'Gauthier',
                'email' => 'EdmondCGauthier@dayrep.com',
                'phone_no' => '(026) 9831-706',
                'address' => '42 Mountfield Terrace',
                'tax_number' => '315-02',
                'client_category_id' => '4',
                'notes' => 'Mozilla/5.0 (X11; Linux x86_64)',
                'logo' => 'V8IxTOGuQq.jpg',
                'currency_id' =>2,
                'focal_name' => 'Theige',
                'focal_phone_no' => '471 647 886',
                'focal_email' => 'Theige@gmail.com',
                'website' => 'BasketballLocator.de'
            ),
            array(
                'id' => '5',
                'name' => 'Corey D',
                'short_name' => 'Lewis',
                'email' => 'CoreyDLewis@armyspy.com',
                'phone_no' => '05944 33 15 31',
                'address' => 'Lietzenburger Straße 22',
                'tax_number' => '819-65',
                'client_category_id' => '2',
                'notes' => 'Mozilla/5.0 (X11; Linux x86_64)',
                'logo' => 'XbG4GhvSoJ.jpg',
                'currency_id' => 1,
                'focal_name' => 'Griefa75',
                'focal_phone_no' => '291 347 386',
                'focal_email' => 'AhLohb0W@gmail.com',
                'website' => 'SocialSeekers.co.nz'
            ),
            array(
                'id' => '6',
                'name' => 'Alice C',
                'short_name' => 'Jones',
                'email' => 'AliceCJones@jourrapide.com',
                'phone_no' => '(026) 7785-282',
                'address' => '210 Haineswood Lane Avonhead Christchurch 8042',
                'tax_number' => '129-06',
                'client_category_id' => '4',
                'notes' => 'Mozilla/5.0 (X11; Linux x86_64)',
                'logo' => 'yfaGJrrijM.png',
                'currency_id' => 2,
                'focal_name' => 'Donsfult',
                'focal_phone_no' => '471 647 886',
                'focal_email' => 'Donsfult@gmail.com',
                'website' => 'JudoLocator.co.nz'
            ),
        );


        Client::insert($clients);
    }
}
