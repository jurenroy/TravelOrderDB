<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accounts')->insert([
            ['account_id' => 20, 'type_id' => 1, 'name_id' => 76, 'email' => 'admin@admin.admin', 'password' => 'U2FsdGVkX1+AFu9n2L0g3E9GT3xIuCs6Hnzq4DLRuyc=', 'signature' => 'images/xL7XVjP6ZloSsO98Z6A1zoye3uCqYaWuqp4SJZHl.jpg'],
            ['account_id' => 24, 'type_id' => 1, 'name_id' => 76, 'email' => 'admin@gmail.com', 'password' => 'Admin123', 'signature' => NULL],
        ]);
    }
}
