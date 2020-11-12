<?php
namespace Database\Seeders;
use App\Models\Product;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        if(!Product::count()) {
            $data = [
                [
                    'product_name'=>'Ube Halaya Tub',
                    'product_price'=> 499
                ],
                [
                    'product_name'=>'Ube Halaya Jar',
                    'product_price'=> 150
                ],
        
            ];
            Product::insert($data);
        }
    }
}
