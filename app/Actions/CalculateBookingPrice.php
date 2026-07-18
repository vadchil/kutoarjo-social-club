<?php

namespace App\Actions;

use App\Models\PricingRule;
use App\Models\SiteSetting;
use InvalidArgumentException;

class CalculateBookingPrice
{
    /**
     * Calculate price for booking duration.
     *
     * @throws InvalidArgumentException
     */
    final public function execute(string $activityType, mixed $startAt, mixed $endAt, int $quantity = 1): array
    {
        $timezoneSetting = SiteSetting::where('key', 'timezone')->first();
        $tz = ($timezoneSetting && isset($timezoneSetting->value['value']))
            ? $timezoneSetting->value['value']
            : 'Asia/Jakarta';

        $start = $startAt instanceof \Illuminate\Support\Carbon
            ? $startAt->copy()->setTimezone($tz)
            : \Illuminate\Support\Carbon::parse($startAt, $tz);

        $end = $endAt instanceof \Illuminate\Support\Carbon
            ? $endAt->copy()->setTimezone($tz)
            : \Illuminate\Support\Carbon::parse($endAt, $tz);

        if ($end->lte($start)) {
            throw new InvalidArgumentException('End time must be after start time.');
        }

        $durationMinutes = $start->diffInMinutes($end);

        $rules = PricingRule::where('activity_type', $activityType)
            ->where('is_active', true)
            ->orderBy('effective_from', 'desc')
            ->get()
            ->map(function (PricingRule $rule) use ($tz) {
                $rule->local_effective_from = \Illuminate\Support\Carbon::parse($rule->effective_from->toDateTimeString(), $tz);
                $rule->local_effective_until = $rule->effective_until
                    ? \Illuminate\Support\Carbon::parse($rule->effective_until->toDateTimeString(), $tz)
                    : null;

                return $rule;
            });

        if ($rules->isEmpty()) {
            throw new InvalidArgumentException("No active pricing rules found for {$activityType}.");
        }

        $totalAccumulated = 0.0;
        $startingHourlyPrice = null;

        // ponytail: minute-by-minute loop used for simplicity and boundary-correctness. If duration scale exceeds 24h, refactor to chunk-based calculation.
        for ($m = 0; $m < $durationMinutes; $m++) {
            $time = $start->copy()->addMinutes($m);
            $dayType = ($time->dayOfWeekIso >= 6) ? 'weekend' : 'weekday';

            $rule = $rules->first(function (PricingRule $rule) use ($dayType, $time) {
                return $rule->day_type === $dayType
                    && $rule->local_effective_from->lte($time)
                    && (is_null($rule->local_effective_until) || $rule->local_effective_until->gt($time));
            });

            if (! $rule) {
                throw new InvalidArgumentException("No active pricing rule found for {$activityType} on {$dayType} at {$time->toDateTimeString()}.");
            }

            if ($m === 0) {
                $startingHourlyPrice = $rule->price_per_hour;
            }

            $totalAccumulated += $rule->price_per_hour / 60.0;
        }

        return [
            'total_price' => (int) round($totalAccumulated * $quantity),
            'duration_minutes' => $durationMinutes,
            'hourly_price' => $startingHourlyPrice ?? 0,
        ];
    }
}
