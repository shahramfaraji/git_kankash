<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'user' => 'ادمین',
            'name' => 'admin',
            'email' => 'admin@example.com',
            'role' => '1',
            'password' => Hash::make('123456'),
        ]);
        DB::table('users')->insert([
            'user' => 'مهرداد',
            'name' => 'mehrdad',
            'email' => 'mehrdad@example.com',
            'role' => '0',
            'password' => Hash::make('123456'),
        ]);
        DB::table('users')->insert([
            'user' => 'شهرام',
            'name' => 'shahram',
            'email' => 'shahram@example.com',
            'role' => '0',
            'password' => Hash::make('123456'),
        ]);
    }
}