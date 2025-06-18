<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
        ];

           foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        $user = \App\Models\User::find(1);
        if ($user && !$user->hasRole('Super-Admin')) {
            $user->assignRole('Super-Admin');
        }
    }
}
