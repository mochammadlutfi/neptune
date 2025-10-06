<?php

namespace Database\Seeders\Base;


use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Branch;
class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $branch = new Branch();
        $branch->name = 'Main Branch';
        $branch->save();

        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@demo.com';
        $user->username = 'admin';
        $user->branch_id = 1;
        $user->password = Hash::make('admin123');
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole('Admin');

    }
}