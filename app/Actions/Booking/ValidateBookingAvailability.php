<?php

namespace App\Actions\Booking;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class ValidateBookingAvailability
{
    /**
     * Validate billiard table booking availability.
     *
     * @throws InvalidArgumentException
     */
    final public function execute(
        string $billiardTableId,
        mixed $startAt,
        mixed $endAt,
        string $bookingType = 'online',
        ?string $excludeBookingId = null
    ): bool {
        $tzSetting = SiteSetting::where('key', 'timezone')->first();
        $tz = ($tzSetting && isset($tzSetting->value['value']))
            ? $tzSetting->value['value']
            : 'Asia/Jakarta';

        $start = $startAt instanceof Carbon ? $startAt->copy()->setTimezone($tz) : Carbon::parse($startAt, $tz);
        $end = $endAt instanceof Carbon ? $endAt->copy()->setTimezone($tz) : Carbon::parse($endAt, $tz);

        if ($end->lte($start)) {
            throw new InvalidArgumentException('End time must be after start time.');
        }

        $table = BilliardTable::where('id', $billiardTableId)->lockForUpdate()->first();
        if (! $table) {
            throw new InvalidArgumentException('Billiard table not found.');
        }
        if (! $table->is_active) {
            throw new InvalidArgumentException('Billiard table is currently inactive.');
        }

        $opStart = $start->copy()->setTime(9, 0, 0);
        $opEnd = $start->copy()->setTime(24, 0, 0);

        if ($start->lt($opStart) || $end->gt($opEnd)) {
            throw new InvalidArgumentException('Booking must be within operational hours (09:00 - 24:00).');
        }

        $maxDaysSetting = SiteSetting::where('key', 'maximum_booking_days')->first();
        $maxDays = ($maxDaysSetting && isset($maxDaysSetting->value['value'])) ? (int) $maxDaysSetting->value['value'] : 14;
        $maxAllowedDate = Carbon::now($tz)->addDays($maxDays)->endOfDay();

        if ($start->gt($maxAllowedDate)) {
            throw new InvalidArgumentException("Booking cannot be made more than {$maxDays} days in advance.");
        }

        if ($bookingType === 'online') {
            $minAdvanceTime = Carbon::now($tz)->addHour();
            if ($start->lt($minAdvanceTime)) {
                throw new InvalidArgumentException('Online bookings must be made at least 1 hour in advance.');
            }
        }

        $durationMinutes = $start->diffInMinutes($end);
        $minDurationSetting = SiteSetting::where('key', 'minimum_booking_duration')->first();
        $minHours = ($minDurationSetting && isset($minDurationSetting->value['value'])) ? (int) $minDurationSetting->value['value'] : 1;

        $maxDurationSetting = SiteSetting::where('key', 'maximum_booking_duration')->first();
        $maxHours = ($maxDurationSetting && isset($maxDurationSetting->value['value'])) ? (int) $maxDurationSetting->value['value'] : 4;

        if ($durationMinutes < ($minHours * 60) || $durationMinutes > ($maxHours * 60)) {
            throw new InvalidArgumentException("Booking duration must be between {$minHours} and {$maxHours} hours.");
        }

        $hasConflict = Booking::where('billiard_table_id', $billiardTableId)
            ->whereNotIn('status', ['cancelled', 'completed', 'expired'])
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->exists();

        if ($hasConflict) {
            throw new InvalidArgumentException('The billiard table is already booked for the selected time slot.');
        }

        return true;
    }
}
