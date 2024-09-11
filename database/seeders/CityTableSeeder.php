<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->insert([
            'name'=> "ساری",
            'price' => 20000
        ]);
        DB::table('cities')->insert([
            'name'=> "قائم شهر",
            'price' => 30000
        ]);
        DB::table('cities')->insert([
            'name'=> "بابل",
            'price' => 40000
        ]);
        DB::table('cities')->insert([
            'name'=> "امل",
            'price' => 50000
        ]);
    }
}