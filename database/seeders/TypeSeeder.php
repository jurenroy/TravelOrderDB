<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('type')->insert([
            ['type_id' => 1, 'type_name' => 'superuser'],
            ['type_id' => 2, 'type_name' => 'user'],
            ['type_id' => 3, 'type_name' => 'signatories'],
        ]);
    }
}
