<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

            // Master permissions (admin/super admin)
            'view master dashboard',
            'manage all users',
            'manage all content',
            'manage impact projects',
            'view analytics',
            'manage system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $parentRole = Role::create(['name' => 'parent']);
        $parentPermissions = [
            'view parent dashboard',
            'manage children',
            'create assignments',
            'view child progress',
            'set skill requirements',
            'view family impact',
        ];
        $parentRole->givePermissionTo($parentPermissions);

        $childRole = Role::create(['name' => 'child']);
        $childPermissions = [
            'view child dashboard',
            'access learning content',
            'submit assignments',
            'view own progress',
            'view impact contributions',
        ];
        $childRole->givePermissionTo($childPermissions);

        $tutorRole = Role::create(['name' => 'tutor']);
        $tutorPermissions = [
            'view tutor dashboard',
            'manage assigned children',
            'create learning content',
            'review assignments',
            'generate reports',
        ];
        $tutorRole->givePermissionTo($tutorPermissions);

        $masterRole = Role::create(['name' => 'master']);
        $masterRole->givePermissionTo(Permission::all());
    }
}
