<?php

namespace Database\Seeders;

use App\Http\Helpers\AppHelper;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Seed the database with an initial superadmin user and permissions.
     */
    public function run(): void
    {
        // Create or retrieve roles
        $roles = collect(AppHelper::USER)->mapWithKeys(function ($roleName, $roleId) {
            return [$roleId => Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web'])];
        });

        // Create superadmin user if it doesn't exist
        if (!User::where('email', 'superadmin@gmail.com')->exists()) {
            $user = User::create([
                'username' => 'superadmin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('crm123'),
                'staff_id_card' => '1280',
                'family_name' => 'ឌី',
                'name' => 'អេដមីន',
                'family_name_latin' => 'Dy',
                'name_latin' => 'Admin',
                'position' => 'Deallar',
                'area' => 'ច្បារអំពៅ',
                'role_id' => AppHelper::USER_SUPER_ADMIN,
                'phone_no' => '0987876567',
                'type' => AppHelper::ALL,
            ]);

            // Assign superadmin role
            $user->syncRoles($roles[AppHelper::USER_SUPER_ADMIN]->name);
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $roles[AppHelper::USER_SUPER_ADMIN]->id,
            ]);
        }

        // Define and assign permissions for superadmin
        $permissions = [
            'create customer', 'view customer', 'update customer', 'delete customer',
            'create report', 'view report', 'update report', 'delete report',
            'create user', 'view user', 'update user', 'delete user',
            'create role', 'view role', 'update role', 'delete role',
            'create sub-wholesale', 'view sub-wholesale', 'update sub-wholesale', 'delete sub-wholesale',
            'create retail', 'view retail', 'update retail', 'delete retail',
            'create asm', 'view asm', 'update asm', 'delete asm',
            'create se', 'view se', 'update se', 'delete se',
            'create permission', 'view permission', 'update permission', 'delete permission',
            'view dashboard', 'view setting', 'reset password',
        ];

        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web', 'type' => AppHelper::ALL],
                ['type' => AppHelper::ALL]
            );

            $roles[AppHelper::USER_SUPER_ADMIN]->permissions()->syncWithoutDetaching([
                $perm->id => ['type' => AppHelper::ALL],
            ]);
        }
    }
}