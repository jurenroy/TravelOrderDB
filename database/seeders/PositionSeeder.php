<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('position')->insert([
            ['position_id' => 1, 'position_name' => 'Economist II'],
            ['position_id' => 2, 'position_name' => 'Administrative Officer V (HRMO)'],
            ['position_id' => 3, 'position_name' => 'Supervising Geologist'],
            ['position_id' => 4, 'position_name' => 'Administrative Aide IV (Driver)'],
            ['position_id' => 5, 'position_name' => 'Supervising Science Research Specialist'],
            ['position_id' => 6, 'position_name' => 'Geologist II'],
            ['position_id' => 7, 'position_name' => 'Administrative Officer III (Records Officer II)'],
            ['position_id' => 8, 'position_name' => 'Engineer II'],
            ['position_id' => 9, 'position_name' => 'Administrative Officer IV (Information Officer II)'],
            ['position_id' => 10, 'position_name' => 'Senior Science Research Specialist'],
            ['position_id' => 11, 'position_name' => 'Attorney III'],
            ['position_id' => 12, 'position_name' => 'Community Affairs Officer II'],
            ['position_id' => 13, 'position_name' => 'Chief Administrative Officer'],
            ['position_id' => 14, 'position_name' => 'Geologic Aide'],
            ['position_id' => 15, 'position_name' => 'Administrative Assistant III'],
            ['position_id' => 16, 'position_name' => 'Science Research Specialist II'],
            ['position_id' => 17, 'position_name' => 'Chief Science Research Specialist'],
            ['position_id' => 18, 'position_name' => 'Engineer V'],
            ['position_id' => 19, 'position_name' => 'Cartographer II'],
            ['position_id' => 20, 'position_name' => 'Administrative Assistant II (HRMA)'],
            ['position_id' => 21, 'position_name' => 'Administrative Officer III'],
            ['position_id' => 22, 'position_name' => 'Mining Claims Examiner II'],
            ['position_id' => 23, 'position_name' => 'Engineer III'],
            ['position_id' => 24, 'position_name' => 'Senior Environmental Management Specialist'],
            ['position_id' => 25, 'position_name' => 'Senior Geologist'],
            ['position_id' => 26, 'position_name' => 'Planning Officer II'],
            ['position_id' => 27, 'position_name' => 'Engineer IV'],
            ['position_id' => 28, 'position_name' => 'Accountant III'],
            ['position_id' => 29, 'position_name' => 'Chief Geologist'],
            ['position_id' => 30, 'position_name' => 'Mining Claims Examiner III'],
            ['position_id' => 31, 'position_name' => 'Administrative Assistant III (Records Management A)'],
            ['position_id' => 32, 'position_name' => 'Geologist'],
            ['position_id' => 33, 'position_name' => 'Driver-Mechanic'],
            ['position_id' => 34, 'position_name' => 'System Network Administrator'],
            ['position_id' => 35, 'position_name' => 'GIS Specialist B'],
            ['position_id' => 36, 'position_name' => 'Science Research Specialist I'],
            ['position_id' => 37, 'position_name' => 'Office Support Staff/Utility Worker'],
            ['position_id' => 38, 'position_name' => 'Administrative Assistant III/Acting Secretary'],
            ['position_id' => 39, 'position_name' => 'Administrative Assistant III/Accounting Clerk'],
            ['position_id' => 40, 'position_name' => 'Procurement Assistant'],
            ['position_id' => 41, 'position_name' => 'Administrative Assistant III/COA Clerk'],
            ['position_id' => 42, 'position_name' => 'IT Specialist'],
            ['position_id' => 43, 'position_name' => 'Administrative Officer IV (Budget Officer II)'],
            ['position_id' => 44, 'position_name' => 'Administrative Assistant II'],
            ['position_id' => 45, 'position_name' => 'Engineer'],
        ]);
    }
}
