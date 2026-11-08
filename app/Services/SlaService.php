<?php

namespace App\Services;

use App\Models\Priority;

class SlaService
{
    public function __construct(
        private BusinessHoursService $businessHours
    ) {}

    public function calculateDueAt(
        Priority $priority
    ) {
        $rule = $priority->slaRule;

        if (!$rule || !$rule->is_active) {
            throw new \RuntimeException(
                'SLA configuration not found.'
            );
        }

        return $this->businessHours->addBusinessMinutes(
            now(),
            $rule->resolution_minutes
        );
    }
}
