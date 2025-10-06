<?php

namespace Database\Seeders\Base;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'Admin']);

        $list_permission = [
            ['name' => 'user.view'],
            ['name' => 'user.create'],
            ['name' => 'user.update'],
            ['name' => 'user.delete'],

            ['name' => 'branch.view'],
            ['name' => 'branch.create'],
            ['name' => 'branch.update'],
            ['name' => 'branch.delete'],
            
            ['name' => 'permission.view'],
            ['name' => 'permission.create'],
            ['name' => 'permission.update'],
            ['name' => 'permission.delete'],
        ];

        $insert_data = [];
        $time_stamp = Carbon::now()->toDateTimeString();
        foreach ($list_permission as $d) {
            $d['guard_name'] = 'sanctum';
            $d['created_at'] = $time_stamp;
            $insert_data[] = $d;
        }

        $permissions = Permission::insert($insert_data);
        
        $role->syncPermissions($permissions);

    }
}
