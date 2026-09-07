<?php

namespace App\Observers;

use App\Http\Helpers\ActivityLogger;
use App\Models\User;

class UserObserver
{
    /**
     * Fields that should never be stored in activity logs.
     */
    protected array $hiddenFields = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        ActivityLogger::create(
            $user,
            "Created user: {$user->name}"
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = $user->getChanges();

        if (empty($changes)) {
            return;
        }

        // Remove sensitive fields
        $changes = collect($changes)
            ->except($this->hiddenFields)
            ->toArray();

        if (empty($changes)) {
            return;
        }

        $oldValues = [];

        foreach ($changes as $field => $newValue) {
            $oldValues[$field] = $user->getOriginal($field);
        }

        ActivityLogger::update(
            $user,
            $oldValues,
            $changes,
            "Updated user: {$user->name}"
        );
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        ActivityLogger::delete(
            $user,
            "Deleted user: {$user->name}"
        );
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        ActivityLogger::log(
            'restored',
            $user,
            "Restored user: {$user->name}"
        );
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        ActivityLogger::log(
            'force_deleted',
            $user,
            "Force deleted user: {$user->name}"
        );
    }
}