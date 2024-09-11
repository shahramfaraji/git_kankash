<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            'name'=> "شرکت 1"
        ]);
        DB::table('companies')->insert([
            'name'=> "شرکت 2"
        ]);
        DB::table('companies')->insert([
            'name'=> "شرکت 3"
        ]);
        DB::table('companies')->insert([
            'name'=> "شرکت 4"
        ]);
    }
}