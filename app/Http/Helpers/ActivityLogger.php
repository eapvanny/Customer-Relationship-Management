<?php

namespace App\Http\Helpers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    protected static array $ignoredFields = [
        'created_at',
        'updated_at',
        'deleted_at',
        'password',
        'remember_token',
        'api_token',
    ];

    public static function log(
        string $action,
        ?Model $subject = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ActivityLog {

        $oldValues = self::cleanValues($oldValues);
        $newValues = self::cleanValues($newValues);

        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,

            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),

            'old_values' => $oldValues,
            'new_values' => $newValues,

            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected static function cleanValues(?array $values): ?array
    {
        if (!$values) {
            return $values;
        }

        return collect($values)
            ->except(self::$ignoredFields)
            ->toArray();
    }

    public static function create(
        Model $subject,
        ?string $description = null
    ): ActivityLog {
        return self::log(
            'create',
            $subject,
            $description ?? 'Created ' . class_basename($subject),
            null,
            $subject->getAttributes()
        );
    }

    public static function update(
        Model $subject,
        array $oldValues,
        array $newValues,
        ?string $description = null
    ): ActivityLog {
        return self::log(
            'update',
            $subject,
            $description ?? 'Updated ' . class_basename($subject),
            $oldValues,
            $newValues
        );
    }

    public static function delete(
        Model $subject,
        ?string $description = null
    ): ActivityLog {
        return self::log(
            'delete',
            $subject,
            $description ?? 'Deleted ' . class_basename($subject),
            $subject->getAttributes(),
            null
        );
    }

    public static function login(?Model $user = null): ActivityLog
    {
        $user ??= Auth::user();

        return self::log(
            'login',
            $user,
            'User logged in'
        );
    }

    public static function logout(?Model $user = null): ActivityLog
    {
        $user ??= Auth::user();

        return self::log(
            'logout',
            $user,
            'User logged out'
        );
    }
}