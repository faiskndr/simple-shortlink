<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=>'Nona',
            'username'=>'nona',
            'email'=>'nona@gmail.com',
            'password'=>Hash::make('password'),
            'is_admin'=>true
        ]);

        User::create([
            'name'=>'Attaya',
            'username'=>'attaya',
            'email'=>'attaya@gmail.com',
            'password'=>Hash::make('password'),
            'is_admin'=>false
        ]);
    }
}
