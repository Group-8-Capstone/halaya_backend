<?php
namespace Database\Seeders;

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
        $this->call(ProductSeeder::class);
        // User::factory(10)->create();
        // DB::table('orders')->insert([
        //     'customer_name' => Str::random(10),
        //     'customer_address' => Str::random(10).'@gmail.com',
        //     'contact_number' => Hash::make('password'),
        //     'order_quantity' => Hash::make('password'),
        //     'delivery_date' => Hash::make('password'),
        //     'order_status' => Hash::make('password'),
        // ]);
    }
}
