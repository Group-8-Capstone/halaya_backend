<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Order::count()) {
            $data = [
                [
                    'customer_id' => '1',
                    'receiver_name' => 'Ivana',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '13', 
                    'ubeHalayaTub_qty' => '1', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473'
                ],
                [
                    'customer_id' => '2',
                    'receiver_name' => 'Alawi',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '9', 
                    'ubeHalayaTub_qty' => '3', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473'
                ],
                [
                    'customer_id' => '3',
                    'receiver_name' => 'Pokwang',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '40', 
                    'ubeHalayaTub_qty' => '1', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473'
                ],
                [
                    'customer_id' => '1',
                    'receiver_name' => 'Tekla',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '38', 
                    'ubeHalayaTub_qty' => '0', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473',
                ],
                [
                    'customer_id' => '1',
                    'receiver_name' => 'Ivana',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '42', 
                    'ubeHalayaTub_qty' => '2', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473',
                ],
                [
                    'customer_id' => '1',
                    'receiver_name' => 'Ivana',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '48', 
                    'ubeHalayaTub_qty' => '0', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473',
                ],
                [
                    'customer_id' => '1',
                    'receiver_name' => 'Ivana',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '0', 
                    'ubeHalayaTub_qty' => '3', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473',
                ],
                [
                    'customer_id' => '1',
                    'receiver_name' => 'Ivana',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '50', 
                    'ubeHalayaTub_qty' => '1', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473',
                ],
                [
                    'customer_id' => '1',
                    'receiver_name' => 'Ivana',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '96', 
                    'ubeHalayaTub_qty' => '0', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473',
                ],
                [
                    'customer_id' => '1',
                    'receiver_name' => 'Ivana',
                    'building_or_street' => 'Nasipit Road',
                    'barangay' => 'Talamban',
                    'city_or_municipality' => 'Cebu City',
                    'province' => 'Cebu',
                    'contact_number' => '09123456789',
                    'ubeHalayaJar_qty' => '0', 
                    'ubeHalayaTub_qty' => '4', 
                    'preferred_delivery_date' => '2020-11-10',
                    'order_status' => 'On order',
                    'distance' => '3.1038473',
                ],
                
            ];
            Order::insert($data);
        }
    }
}
