<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('leaveform')->insert([
            ['name_id' => 5, 'position_id' => 5, 'division_id' => 5, 'station' => 'MGB-X', 'destination' => 'Carmen', 'purpose' => 'Vacation', 'departure' => '2024-04-01', 'arrival' => '2024-04-07', 'date' => '2024-03-20', 'note' => 'N/A'],
            ['name_id' => 3, 'position_id' => 3, 'division_id' => 3, 'station' => 'MGB-X', 'destination' => 'Iponan', 'purpose' => 'Medical Check-up', 'departure' => '2024-04-01', 'arrival' => '2024-04-03', 'date' => '2024-03-21', 'note' => 'N/A'],
            ['name_id' => 2, 'position_id' => 2, 'division_id' => 2, 'station' => 'MGB-X', 'destination' => 'Gingoog', 'purpose' => 'Family Event', 'departure' => '2024-04-05', 'arrival' => '2024-04-10', 'date' => '2024-03-22', 'note' => 'N/A'],
            ['name_id' => 4, 'position_id' => 4, 'division_id' => 4, 'station' => 'MGB-X', 'destination' => 'Minaog', 'purpose' => 'Personal', 'departure' => '2024-04-10', 'arrival' => '2024-04-15', 'date' => '2024-03-23', 'note' => 'N/A'],
            ['name_id' => 6, 'position_id' => 6, 'division_id' => 6, 'station' => 'MGB-X', 'destination' => 'Carmen', 'purpose' => 'Leave of Absence', 'departure' => '2024-04-15', 'arrival' => '2024-04-20', 'date' => '2024-03-24', 'note' => 'N/A'],
            ['name_id' => 8, 'position_id' => 8, 'division_id' => 8, 'station' => 'MGB-X', 'destination' => 'Mandaluyong', 'purpose' => 'Business Trip', 'departure' => '2024-04-20', 'arrival' => '2024-04-25', 'date' => '2024-03-25', 'note' => 'N/A'],
            ['name_id' => 10, 'position_id' => 10, 'division_id' => 10, 'station' => 'MGB-X', 'destination' => 'Claveria', 'purpose' => 'Conference', 'departure' => '2024-04-25', 'arrival' => '2024-04-30', 'date' => '2024-03-26', 'note' => 'N/A'],
            ['name_id' => 12, 'position_id' => 12, 'division_id' => 12, 'station' => 'MGB-X', 'destination' => 'Minaog', 'purpose' => 'Seminar', 'departure' => '2024-04-30', 'arrival' => '2024-05-05', 'date' => '2024-03-27', 'note' => 'N/A'],
        ]);
    }
}
