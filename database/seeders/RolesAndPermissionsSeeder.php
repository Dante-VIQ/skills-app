<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        // Create permissions
        $permissions = [
            // Parent permissions
            'view parent dashboard',
            'manage children',
            'create assignments',
            'view child progress',
            'set skill requirements',
            'view family impact',

            // Child permissions
            'view child dashboard',
            'access learning content',
            'submit assignments',
            'view own progress',
            'view impact contributions',

            // Tutor permissions
            'view tutor dashboard',
            'manage assigned children',
            'create learning content',
            'review assignments',
            'generate reports',

            // Master permissions
            'view master dashboard',
            'manage all users',
            'manage all content',
            'manage impact projects',
            'view analytics',
            'manage system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => $guard]
            );
        }

        // Create roles with guard and assign permissions
        $parentRole = Role::firstOrCreate(['name' => 'parent', 'guard_name' => $guard]);
        $parentRole->syncPermissions([
            'view parent dashboard',
            'manage children',
            'create assignments',
            'view child progress',
            'set skill requirements',
            'view family impact',
        ]);

        $childRole = Role::firstOrCreate(['name' => 'child', 'guard_name' => $guard]);
        $childRole->syncPermissions([
            'view child dashboard',
            'access learning content',
            'submit assignments',
            'view own progress',
            'view impact contributions',
        ]);

        $tutorRole = Role::firstOrCreate(['name' => 'tutor', 'guard_name' => $guard]);
        $tutorRole->syncPermissions([
            'view tutor dashboard',
            'manage assigned children',
            'create learning content',
            'review assignments',
            'generate reports',
        ]);

        $masterRole = Role::firstOrCreate(['name' => 'master', 'guard_name' => $guard]);
        $masterRole->syncPermissions(Permission::where('guard_name', $guard)->get());
    }
}