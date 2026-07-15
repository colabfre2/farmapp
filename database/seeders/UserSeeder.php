<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
       
        DB::table('users')->insert([
            [
                'name'       => 'Admin FarmApp',
                'email'      => 'admin@farmapp.com',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'name'       => 'Alice Johnson',
                'email'      => 'buyer@farmapp.com',
                'password'   => Hash::make('password'),
                'role'       => 'buyer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
