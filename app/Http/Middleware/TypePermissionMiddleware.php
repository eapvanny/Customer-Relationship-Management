<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Models\Permission;

class TypePermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission, $guard = null)
    {
        $authGuard = app('auth')->guard($guard);

        if ($authGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $user = $authGuard->user();
        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        foreach ($permissions as $perm) {
            // Find permission with matching name and user type
            $permissionModel = Permission::where('name', $perm)
                ->where('type', $user->type)
                ->first();

            if ($permissionModel && $user->hasPermissionTo($permissionModel)) {
                // Check if the permission is assigned to the user's role with the correct type
                $hasPermissionWithType = $user->roles()->whereHas('permissions', function ($query) use ($permissionModel, $user) {
                    $query->where('permissions.id', $permissionModel->id)
                          ->where('role_has_permissions.type', $user->type);
                })->exists();

                if ($hasPermissionWithType) {
                    return $next($request);
                }
            }

            // Fallback: Check for ALL type permissions (Super Admin)
            $allTypePermission = Permission::where('name', $perm)
                ->where('type', \App\Http\Helpers\AppHelper::ALL)
                ->first();

            if ($allTypePermission && $user->hasPermissionTo($allTypePermission)) {
                $hasPermissionWithAllType = $user->roles()->whereHas('permissions', function ($query) use ($allTypePermission) {
                    $query->where('permissions.id', $allTypePermission->id)
                          ->where('role_has_permissions.type', \App\Http\Helpers\AppHelper::ALL);
                })->exists();

                if ($hasPermissionWithAllType) {
                    return $next($request);
                }
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}