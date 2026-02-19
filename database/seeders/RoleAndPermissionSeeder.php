<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'web';

        $permissions = [
            'manage pages',
            'manage blog',
            'manage settings',
            'view contact submissions',
            'delete contact submissions',
            'manage users',
            'manage roles',
            'manage feature flags',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $adminRole->syncPermissions(Permission::where('guard_name', $guard)->pluck('name'));
    }
}
