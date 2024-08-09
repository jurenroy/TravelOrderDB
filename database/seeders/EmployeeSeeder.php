<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee')->insert([
            ['employee_id' => 1, 'name_id' => 1, 'position_id' => 1, 'division_id' => 1, 'chief' => 0, 'isActive' => 'null', 'rd' => NULL],
            ['employee_id' => 2, 'name_id' => 2, 'position_id' => 2, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 3, 'name_id' => 3, 'position_id' => 3, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 4, 'name_id' => 4, 'position_id' => 4, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 5, 'name_id' => 5, 'position_id' => 5, 'division_id' => 4, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 6, 'name_id' => 6, 'position_id' => 6, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 7, 'name_id' => 7, 'position_id' => 7, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 8, 'name_id' => 8, 'position_id' => 3, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 9, 'name_id' => 9, 'position_id' => 8, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 10, 'name_id' => 10, 'position_id' => 9, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 11, 'name_id' => 11, 'position_id' => 10, 'division_id' => 4, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 12, 'name_id' => 12, 'position_id' => 5, 'division_id' => 4, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 13, 'name_id' => 13, 'position_id' => 11, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 14, 'name_id' => 14, 'position_id' => 12, 'division_id' => 4, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 15, 'name_id' => 15, 'position_id' => 13, 'division_id' => 2, 'chief' => 1, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 16, 'name_id' => 16, 'position_id' => 10, 'division_id' => 4, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 17, 'name_id' => 18, 'position_id' => 15, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 18, 'name_id' => 17, 'position_id' => 14, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 19, 'name_id' => 19, 'position_id' => 16, 'division_id' => 4, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 20, 'name_id' => 20, 'position_id' => 17, 'division_id' => 5, 'chief' => 1, 'isActive' => '0', 'rd' => 'in'],
            ['employee_id' => 21, 'name_id' => 21, 'position_id' => 18, 'division_id' => 1, 'chief' => 1, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 22, 'name_id' => 22, 'position_id' => 19, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 23, 'name_id' => 25, 'position_id' => 44, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 24, 'name_id' => 26, 'position_id' => 21, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 25, 'name_id' => 27, 'position_id' => 14, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 26, 'name_id' => 28, 'position_id' => 22, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 27, 'name_id' => 29, 'position_id' => 23, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 28, 'name_id' => 30, 'position_id' => 21, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 29, 'name_id' => 31, 'position_id' => 24, 'division_id' => 4, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 30, 'name_id' => 32, 'position_id' => 25, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 31, 'name_id' => 33, 'position_id' => 3, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 32, 'name_id' => 34, 'position_id' => 23, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 33, 'name_id' => 35, 'position_id' => 15, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 34, 'name_id' => 36, 'position_id' => 27, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 35, 'name_id' => 37, 'position_id' => 26, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 36, 'name_id' => 38, 'position_id' => 8, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 37, 'name_id' => 39, 'position_id' => 28, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 38, 'name_id' => 40, 'position_id' => 6, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 39, 'name_id' => 41, 'position_id' => 8, 'division_id' => 1, 'chief' => 0, 'isActive' => 'null', 'rd' => NULL],
            ['employee_id' => 40, 'name_id' => 42, 'position_id' => 3, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 41, 'name_id' => 43, 'position_id' => 25, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 42, 'name_id' => 44, 'position_id' => 23, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 43, 'name_id' => 45, 'position_id' => 29, 'division_id' => 3, 'chief' => 1, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 44, 'name_id' => 46, 'position_id' => 30, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 45, 'name_id' => 47, 'position_id' => 10, 'division_id' => 4, 'chief' => 0, 'isActive' => 'null', 'rd' => NULL],
            ['employee_id' => 46, 'name_id' => 48, 'position_id' => 10, 'division_id' => 1, 'chief' => 1, 'isActive' => 'null', 'rd' => NULL],
            ['employee_id' => 47, 'name_id' => 49, 'position_id' => 31, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 48, 'name_id' => 50, 'position_id' => 32, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 49, 'name_id' => 51, 'position_id' => 33, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 50, 'name_id' => 52, 'position_id' => 15, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 51, 'name_id' => 53, 'position_id' => 34, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 52, 'name_id' => 54, 'position_id' => 35, 'division_id' => 3, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 53, 'name_id' => 55, 'position_id' => 16, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 54, 'name_id' => 56, 'position_id' => 36, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 55, 'name_id' => 57, 'position_id' => 37, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 56, 'name_id' => 58, 'position_id' => 16, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 57, 'name_id' => 59, 'position_id' => 16, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 58, 'name_id' => 60, 'position_id' => 16, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 59, 'name_id' => 61, 'position_id' => 35, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 60, 'name_id' => 62, 'position_id' => 39, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 61, 'name_id' => 64, 'position_id' => 39, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 62, 'name_id' => 23, 'position_id' => 43, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 63, 'name_id' => 63, 'position_id' => 45, 'division_id' => 4, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 64, 'name_id' => 65, 'position_id' => 45, 'division_id' => 1, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 65, 'name_id' => 24, 'position_id' => 20, 'division_id' => 2, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
            ['employee_id' => 68, 'name_id' => 76, 'position_id' => 42, 'division_id' => 5, 'chief' => 0, 'isActive' => '0', 'rd' => NULL],
        ]);
    }
}
