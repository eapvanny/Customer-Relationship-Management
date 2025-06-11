<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
// use Illumiate\Support\Facade\URL;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // if(env('APP_ENV') == 'local'){
        //     URL::forceScheme('https');
        // }
        Blade::directive('hasTypePermission', function ($permission) {
            return "<?php
        \$user = auth()->user();
        \$permissionName = $permission;
        \$hasPermission = false;

        if (\$user) {
            \$permissionModel = \\Spatie\\Permission\\Models\\Permission::where('name', \$permissionName)
                ->where('type', \$user->type)
                ->first();

            if (\$permissionModel && \$user->hasPermissionTo(\$permissionModel)) {
                \$hasPermission = \$user->roles()->whereHas('permissions', function (\$query) use (\$permissionModel, \$user) {
                    \$query->where('permissions.id', \$permissionModel->id)
                          ->where('role_has_permissions.type', \$user->type);
                })->exists();
            }

            if (!\$hasPermission) {
                \$allTypePermission = \\Spatie\\Permission\\Models\\Permission::where('name', \$permissionName)
                    ->where('type', \\App\\Http\\Helpers\\AppHelper::ALL)
                    ->first();

                if (\$allTypePermission && \$user->hasPermissionTo(\$allTypePermission)) {
                    \$hasPermission = \$user->roles()->whereHas('permissions', function (\$query) use (\$allTypePermission) {
                        \$query->where('permissions.id', \$allTypePermission->id)
                              ->where('role_has_permissions.type', \\App\\Http\\Helpers\\AppHelper::ALL);
                    })->exists();
                }
            }
        }

        if (\$hasPermission):
    ?>";
        });


        Blade::directive('endHasTypePermission', function () {
            return "<?php endif; ?>";
        });
    }
}
