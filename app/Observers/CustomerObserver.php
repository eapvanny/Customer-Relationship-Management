<?php

namespace App\Observers;

use App\Http\Helpers\ActivityLogger;
use App\Models\Customer;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        ActivityLogger::create(
            $customer,
            "Created customer: {$customer->name}"
        );
    }

    public function updated(Customer $customer): void
    {
        $changes = $customer->getChanges();

        if (empty($changes)) {
            return;
        }

        $oldValues = [];

        foreach ($changes as $field => $newValue) {
            $oldValues[$field] = $customer->getOriginal($field);
        }

        ActivityLogger::update(
            $customer,
            $oldValues,
            $changes,
            "Updated customer: {$customer->name}"
        );
    }

    public function deleted(Customer $customer): void
    {
        ActivityLogger::delete(
            $customer,
            "Deleted customer: {$customer->name}"
        );
    }
}
