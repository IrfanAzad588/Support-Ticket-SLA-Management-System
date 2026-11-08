<?php

namespace App\Services;

use App\Models\BusinessHour;
use App\Models\Holiday;
use Carbon\Carbon;

class BusinessHoursService
{
    public function addBusinessMinutes(
        Carbon $start,
        int $minutes
    ): Carbon {
        $cursor = $start->copy();
        $remaining = $minutes;

        while ($remaining > 0) {

            // Skip company holidays.
            if ($this->isHoliday($cursor)) {
                $cursor->addDay()->startOfDay();
                continue;
            }

            // Get business hours for this weekday.
            $schedule = BusinessHour::where(
                'weekday',
                $cursor->dayOfWeek
            )->first();

            if (!$schedule || !$schedule->is_working_day) {
                $cursor->addDay()->startOfDay();
                continue;
            }

            $opening = $cursor->copy()->setTimeFromTimeString(
                $schedule->start_time
            );

            $closing = $cursor->copy()->setTimeFromTimeString(
                $schedule->end_time
            );

            // Before office opens.
            if ($cursor->lt($opening)) {
                $cursor = $opening;
            }

            // Office has already closed.
            if ($cursor->gte($closing)) {
                $cursor->addDay()->startOfDay();
                continue;
            }

            // Available working minutes for this day.
            $available = (int) $cursor->diffInMinutes($closing);

            if ($remaining <= $available) {
                return $cursor->addMinutes($remaining);
            }

            $remaining -= $available;

            // Continue from next day.
            $cursor->addDay()->startOfDay();
        }

        return $cursor;
    }

    private function isHoliday(Carbon $date): bool
    {
        return Holiday::whereDate(
            'holiday_date',
            $date->toDateString()
        )->exists();
    }
}
