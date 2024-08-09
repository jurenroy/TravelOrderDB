<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('division')->insert([
            ['division_id' => 1, 'division_name' => 'MMD'],
            ['division_id' => 2, 'division_name' => 'FAD'],
            ['division_id' => 3, 'division_name' => 'GD'],
            ['division_id' => 4, 'division_name' => 'MSESDD'],
            ['division_id' => 5, 'division_name' => 'ORD'],
        ]);
    }
}
