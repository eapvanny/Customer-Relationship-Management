<?php

namespace Database\Seeders;

use App\Http\Helpers\AppHelper;
use App\Models\Status;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [];
        foreach (AppHelper::USER as $roleId => $roleName) {
            $roles[$roleId] = Role::firstOrCreate(['name' => $roleName]);
        }

        if (User::where('email', 'superadmin@gmail.com')->doesntExist()) {
            $user = User::create([
                'username' => 'superadmin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('crm123'),
                // 'photo' => '123.jpg',
                'staff_id_card' => '1280',
                'family_name' => 'ឌី',
                'name' => 'អេដមីន',
                'family_name_latin' => 'Dy',
                'name_latin' => 'Admin',
                'position' => 'Deallar',
                'area' => 'ច្បារអំពៅ',
                'role_id' => AppHelper::USER_SUPER_ADMIN,
                'phone_no' => '0987876567',
            ]);

            // Assign role using Spatie
            $user->syncRoles($roles[AppHelper::USER_SUPER_ADMIN]->name);
            
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $roles[AppHelper::USER_SUPER_ADMIN]->id,
            ]);
        }
        $permissions = [    
            'create report', 'view report', 'update report', 'delete report',
            'create user', 'view user', 'update user', 'delete user',
            'create role', 'view role', 'update role', 'delete role',
            'create permission', 'view permission', 'update permission', 'delete permission',
        ];

        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            $roles[AppHelper::USER_SUPER_ADMIN]->givePermissionTo($perm);
        }
    }
}
