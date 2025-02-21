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
        // Ensure the role exists before assigning it
        if (Role::where('name', 'Super Admin')->doesntExist()) {
            $adminRole = Role::create(['name' => 'Super Admin']);
        } else {
            $adminRole = Role::where('name', 'Super Admin')->first();
        }

        if (User::where('email', 'admin@gmail.com')->doesntExist()) {
            $user = User::create([
                'name' => 'Mr. Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('demo123'),
                'photo' => '123.jpg',
                'id_card' => '1280',
                'family_name' => 'ឌី',
                'name' => 'អេដមីន',
                'family_name_latin' => 'Dy',
                'name_latin' => 'Admin',
                'role_id' => AppHelper::USER_SUPER_ADMIN,
                'phone_no' => '0987876567',
            ]);

            // Assign role using Spatie
            $user->syncRoles($adminRole->name);
            
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $adminRole->id,
            ]);
        }
        $permissions = [    
            'create report', 'view report', 'update report', 'delete report',
            'create user', 'view user', 'update user', 'delete user',
            'create role', 'view role', 'update role', 'delete role',
            'create permission', 'view permission', 'update permission', 'delete permission',
        ];

        foreach ($permissions as $permission) {
            // Ensure permission exists
            $perm = Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
            // Assign permission to role
            $adminRole->givePermissionTo($perm);
        }
    }
}
