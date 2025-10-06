<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permission::truncate();

        $list_permission = [
            ['name' => 'user.view'],
            ['name' => 'user.create'],
            ['name' => 'user.update'],
            ['name' => 'user.delete'],

            ['name' => 'branch.view'],
            ['name' => 'branch.create'],
            ['name' => 'branch.update'],
            ['name' => 'branch.delete'],
            
            ['name' => 'role_permission.view'],
            ['name' => 'role_permission.create'],
            ['name' => 'role_permission.update'],
            ['name' => 'role_permission.delete'],
        ];

        $insert_data = [];
        $time_stamp = Carbon::now()->toDateTimeString();
        foreach ($list_permission as $d) {
            $d['guard_name'] = 'web';
            $d['created_at'] = $time_stamp;
            $insert_data[] = $d;
        }

        Permission::insert($insert_data);


    }
}
