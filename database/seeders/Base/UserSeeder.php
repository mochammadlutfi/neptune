<?php

namespace Database\Seeders\Base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // check if table is empty
        if (DB::table('users')->count() == 0) {
            DB::table('users')->insert([
                [
                    'name' => 'Super Admin',
                    'email' => 'admin@admin.com',
                    'username' => 'admin',
                    'email_verified_at' => Carbon::now(),
                    'password' => Hash::make('admin123'),
                ],
            ]);
        }
    }
}