<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait NotifiesBusinessChanges
{
    // Keep the business write and its persistent deliveries on the same connection/transaction.
    public function save(array $options = [])
    {
        return $this->getConnection()->transaction(function () use ($options) {
            if (!$this->exists || $this->isDirty()) {
                $this->notification_event_key = (string) Str::uuid();
            }

            return parent::save($options);
        });
    }
}
