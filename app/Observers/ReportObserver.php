<?php

namespace App\Observers;

use App\Http\Helpers\ActivityLogger;
use App\Models\Report;

class ReportObserver
{
    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        ActivityLogger::create(
            $report,
            "Created report #{$report->id}"
        );
    }

    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
    {
        $changes = $report->getChanges();

        if (empty($changes)) {
            return;
        }

        $oldValues = [];

        foreach ($changes as $field => $newValue) {
            $oldValues[$field] = $report->getOriginal($field);
        }

        ActivityLogger::update(
            $report,
            $oldValues,
            $changes,
            "Updated report #{$report->id}"
        );
    }

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        ActivityLogger::delete(
            $report,
            "Deleted report #{$report->id}"
        );
    }

    /**
     * Handle the Report "restored" event.
     */
    public function restored(Report $report): void
    {
        ActivityLogger::log(
            'restored',
            $report,
            "Restored report #{$report->id}"
        );
    }

    /**
     * Handle the Report "force deleted" event.
     */
    public function forceDeleted(Report $report): void
    {
        ActivityLogger::log(
            'force_deleted',
            $report,
            "Force deleted report #{$report->id}",
            $report->getAttributes(),
            null
        );
    }
}