<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $access = [
            'masteradmin' => [
                //! DASHBOARD
                'dashboard:manage_dashboard',

                //! USER
                'user:manage_user',
                'user:register_user',
                'user:edit_user',
                'user:delete_user',

                //! ROLE
                'role:manage_role',
                'role:register_role',
                'role:edit_role',
                'role:delete_role',

                //! REFERENCE
                'reference:manage_reference',
                'reference:register_reference',
                'reference:edit_reference',
                'reference:delete_reference',

                //! CHECKLIST
                'checklist:manage_checklist',
                'checklist:register_checklist',
                'checklist:edit_checklist',
                'checklist:delete_checklist',

                //! MAINTENANCE
                'maintenance:manage_maintenance',
                'maintenance:register_maintenance',
                'maintenance:edit_maintenance',
                'maintenance:delete_maintenance',
            ],
            'admin' => [
                //! DASHBOARD
                'dashboard:manage_dashboard',

                //! USER
                'user:manage_user',
                'user:register_user',
                'user:edit_user',
                'user:delete_user',

                //! ROLE
                'role:manage_role',
                'role:register_role',
                'role:edit_role',
                'role:delete_role',

                //! REFERENCE
                'reference:manage_reference',
                'reference:register_reference',
                'reference:edit_reference',
                'reference:delete_reference',

                //! CHECKLIST
                'checklist:manage_checklist',
                'checklist:register_checklist',
                'checklist:edit_checklist',
                'checklist:delete_checklist',

                //! MAINTENANCE
                'maintenance:manage_maintenance',
                'maintenance:register_maintenance',
                'maintenance:edit_maintenance',
                'maintenance:delete_maintenance',
            ],
            'staff' => [
                //! DASHBOARD
                'dashboard:manage_dashboard',

                //! CHECKLIST
                'checklist:manage_checklist',
                'checklist:register_checklist',
                'checklist:edit_checklist',
                'checklist:delete_checklist',

                //! MAINTENANCE
                'maintenance:manage_maintenance',
                'maintenance:register_maintenance',
                'maintenance:edit_maintenance',
                'maintenance:delete_maintenance',
            ],
        ];

        foreach ($access as $peranan => $permissions) {
            $perananModel = Role::firstOrCreate(
                ['name' => $peranan],
                [
                    'name' => $peranan,
                    'label' => Str::of($peranan)->title(),
                    'guard_name' => "api",
                ],
            );

            collect($permissions)->each(function ($permission) use ($perananModel) {
                list($module, $permissionName) = explode(':', $permission);
                $permissionModel = Permission::firstOrCreate([
                    'name' => $permission,
                    'module' => Str::of($module)->title(),
                    'label' => Str::of($permissionName)->title(),
                    'guard_name' => "api",
                ]);
                $perananModel->givePermissionTo($permissionModel);
            });
        }

    }
}
