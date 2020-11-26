<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!User::count()) {
            $data = [
                [
                    'username'=>'admin',
                    'role'=> 'admin',
                    'phone'=>'09079077210',
                    'password'=>Hash::make('Cl@rk_adm1n'),
                    'remember_token'=>Str::random(10)
                ],
                [
                    'username'=>'rider',
                    'role'=> 'driver',
                    'phone'=> '09554527035',
                    'password'=> Hash::make('R1der_halaya'),
                    'remember_token'=>Str::random(10)
                ],
        
            ];
            User::insert($data);
        }
    }
}
