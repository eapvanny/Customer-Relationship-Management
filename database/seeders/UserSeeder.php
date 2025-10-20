<?php

namespace Database\Seeders;

use App\Models\Posm;
use App\Models\User;
use App\Models\UserRole;
use App\Http\Helpers\AppHelper;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update roles
        $roles = collect(AppHelper::USER)->mapWithKeys(function ($roleName, $roleId) {
            $role = Role::updateOrCreate(
                ['id' => $roleId],
                ['name' => $roleName, 'guard_name' => 'web']
            );
            return [$roleId => $role];
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

        // Define permissions for different types
        $permissionGroups = [
            AppHelper::ALL => [
                'create customer',
                'view customer',
                'update customer',
                'delete customer',
                'create report',
                'view report',
                'update report',
                'delete report',
                'create user',
                'view user',
                'update user',
                'delete user',
                'create role',
                'view role',
                'update role',
                'delete role',
                'create sub-wholesale',
                'view sub-wholesale',
                'update sub-wholesale',
                'delete sub-wholesale',
                'take photo sub-wholesale',

                'create retail',
                'view retail',
                'update retail',
                'delete retail',
                'take photo retail',


                'create asm',
                'view asm',
                'update asm',
                'delete asm',
                'create se',
                'view se',
                'update se',
                'delete se',
                'create school',
                'view school',
                'update school',
                'delete school',
                'create restaurant',
                'view restaurant',
                'update restaurant',
                'delete restaurant',
                'create sport club',
                'view sport club',
                'update sport club',
                'delete sport club',
                'create permission',
                'view permission',
                'update permission',
                'delete permission',
                'view dashboard',
                'view setting',
                'reset password',
                'view customer province',
                'create customer province',
                'update customer province',
                'delete customer province',
                'view depo',
                'create depo',
                'update depo',
                'delete depo',
                'create region',
                'view region',
                'update region',
                'delete region',
                'create outlet',
                'view outlet',
                'update outlet',
                'delete outlet',
                'view depot management',
                'create depot management',
                'edit depot management',
                'delete depot management',
                'view posm',
                'create posm',
                'update posm',
                'delete posm',

                'view wholesale',
                'create wholesale',
                'update wholesale',
                'delete wholesale',
                'take photo wholesale',

                'view exclusive',
                'create exclusive',
                'update exclusive',
                'delete exclusive',

                'view marketing',
                'view system operation',

                'view daily sale province',
                'create daily sale province',
                'update daily sale province',
                'delete daily sale province',

            ],
            AppHelper::SALE => [
                'create customer',
                'view customer',
                'update customer',
                'delete customer',
                'create report',
                'view report',
                'update report',
                'delete report',
                'create user',
                'view user',
                'update user',
                'delete user',
                'view dashboard',
            ],
            AppHelper::SE => [
                'create customer',
                'view customer',
                'update customer',
                'delete customer',
                'create report',
                'view report',
                'update report',
                'delete report',
                'create user',
                'view user',
                'update user',
                'delete user',
                'view dashboard',
                'view customer province',
                'create customer province',
                'update customer province',
                'delete customer province',
                'create sub-wholesale',
                'view sub-wholesale',
                'update sub-wholesale',
                'delete sub-wholesale',
                'take photo sub-wholesale',


                'create retail',
                'view retail',
                'update retail',
                'delete retail',
                'take photo retail',
                'create asm',
                'view asm',
                'update asm',
                'delete asm',
                'create se',
                'view se',
                'update se',
                'delete se',
                'create school',
                'view school',
                'update school',
                'delete school',
                'create restaurant',
                'view restaurant',
                'update restaurant',
                'delete restaurant',
                'create sport club',
                'view sport club',
                'update sport club',
                'delete sport club',
                'create region',
                'view region',
                'update region',
                'delete region',
                // 'create outlet',
                // 'view outlet',
                // 'update outlet',
                // 'delete outlet',
                'view depot management',
                'create depot management',
                'edit depot management',
                'delete depot management',
                'view posm',
                'create posm',
                'update posm',
                'delete posm',


                'view wholesale',
                'create wholesale',
                'update wholesale',
                'delete wholesale',
                'take photo wholesale',


                'view exclusive',
                'create exclusive',
                'update exclusive',
                'delete exclusive',

                'view marketing',
                'view system operation',

                'view daily sale province',
                'create daily sale province',
                'update daily sale province',
                'delete daily sale province',
            ],
        ];

        // Create and assign permissions for each type
        // In UserSeeder.php
        foreach ($permissionGroups as $type => $permissions) {
            foreach ($permissions as $permission) {
                $perm = Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => 'web', 'type' => $type],
                    ['type' => $type]
                );

                // Assign permissions to appropriate roles based on type
                if ($type === AppHelper::ALL) {
                    $roles[AppHelper::USER_SUPER_ADMIN]->permissions()->syncWithoutDetaching([
                        $perm->id => ['type' => $type],
                    ]);
                }
            }
        }

        $items = [
            ['en' => 'Umbrella', 'kh' => 'ឆ័ត្រ'],
            ['en' => 'Tumbler', 'kh' => 'កែវទឹក'],
            ['en' => 'Parasol', 'kh' => 'ឆ័ត្រពាណិជ្ជកម្ម'],
            ['en' => 'Jacket', 'kh' => 'អាវក្រៅ'],
            ['en' => 'Bottle holder', 'kh' => 'អ្នកកាន់ដប'],
            ['en' => 'Ice box 200L', 'kh' => 'ប្រអប់ទឹកកក 200 លីត្រ'],
            ['en' => 'Cap Blue', 'kh' => 'មួកពណ៌ខៀវ'],
            ['en' => 'Hat', 'kh' => 'មួក'],
            ['en' => 'Glass cup', 'kh' => 'កែវកញ្ចក់'],
            ['en' => 'Ice Box 27L', 'kh' => 'ប្រអប់ទឹកកក 27 លីត្រ'],
            ['en' => 'Ice Box 45L', 'kh' => 'ប្រអប់ទឹកកក 45 លីត្រ'],
            ['en' => 'T-Shirt (Running)', 'kh' => 'អាវយឺត (រត់)'],
            ['en' => 'Lunch Box', 'kh' => 'ប្រអប់អាហារថ្ងៃត្រង់'],
            ['en' => 'LSK Fan 16" DSF-9163', 'kh' => 'កង្ហា LSK 16" DSF-9163'],
            ['en' => 'Paper Cup (250ml)', 'kh' => 'កែវក្រដាស (250ml)'],
            ['en' => 'Tissue Box', 'kh' => 'ប្រអប់ក្រដាសជូត'],
        ];

        foreach ($items as $item) {
            Posm::firstOrCreate(
                ['name_en' => $item['en']],  // prevent duplicates
                [
                    'name_kh' => $item['kh'],
                    'status' => 1,
                    'created_by' => 1,
                ]
            );
        }
    }
}
