<?php

namespace App\Observers;

use App\Http\Helpers\ActivityLogger;
use App\Models\Depo;

class DepoObserver
{
    /**
     * Handle the Depo "created" event.
     */
    public function created(Depo $depo): void
    {
        ActivityLogger::create(
            $depo,
            "Created depo: {$depo->name}"
        );
    }

    /**
     * Handle the Depo "updated" event.
     */
    public function updated(Depo $depo): void
    {
        $changes = $depo->getChanges();

        if (empty($changes)) {
            return;
        }

        $oldValues = [];

        foreach ($changes as $field => $newValue) {
            $oldValues[$field] = $depo->getOriginal($field);
        }

        ActivityLogger::update(
            $depo,
            $oldValues,
            $changes,
            "Updated depo: {$depo->name}"
        );
    }

    /**
     * Handle the Depo "deleted" event.
     */
    public function deleted(Depo $depo): void
    {
        ActivityLogger::delete(
            $depo,
            "Deleted depo: {$depo->name}"
        );
    }

    /**
     * Handle the Depo "restored" event.
     */
    public function restored(Depo $depo): void
    {
        ActivityLogger::log(
            'restored',
            $depo,
            "Restored depo: {$depo->name}"
        );
    }

    /**
     * Handle the Depo "force deleted" event.
     */
    public function forceDeleted(Depo $depo): void
    {
        ActivityLogger::log(
            'force_deleted',
            $depo,
            "Force deleted depo: {$depo->name}",
            $depo->getAttributes(),
            null
        );
    }
}