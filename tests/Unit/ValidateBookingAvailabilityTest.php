<?php

namespace Tests\Unit;

use App\Actions\Booking\ValidateBookingAvailability;
use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ValidateBookingAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private ValidateBookingAvailability $validator;

    private BilliardTable $table;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ValidateBookingAvailability;

        SiteSetting::factory()->create([
            'key' => 'timezone',
            'value' => ['name' => 'Timezone', 'value' => 'Asia/Jakarta'],
        ]);

        SiteSetting::factory()->create([
            'key' => 'maximum_booking_days',
            'value' => ['name' => 'Max Days', 'value' => 14],
        ]);

        SiteSetting::factory()->create([
            'key' => 'minimum_booking_duration',
            'value' => ['name' => 'Min Duration', 'value' => 1],
        ]);

        SiteSetting::factory()->create([
            'key' => 'maximum_booking_duration',
            'value' => ['name' => 'Max Duration', 'value' => 4],
        ]);

        $this->table = BilliardTable::factory()->create(['is_active' => true]);
    }

    public function test_valid_booking_passes(): void
    {
        $start = now('Asia/Jakarta')->addDays(2)->setTime(10, 0, 0);
        $end = $start->copy()->addHours(2);

        $result = $this->validator->execute($this->table->id, $start, $end);
        $this->assertTrue($result);
    }

    public function test_fails_when_table_inactive(): void
    {
        $this->table->update(['is_active' => false]);
        $start = now('Asia/Jakarta')->addDays(2)->setTime(10, 0, 0);
        $end = $start->copy()->addHours(2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Billiard table is currently inactive.');

        $this->validator->execute($this->table->id, $start, $end);
    }

    public function test_fails_before_operational_hours(): void
    {
        $start = now('Asia/Jakarta')->addDays(2)->setTime(8, 30, 0);
        $end = $start->copy()->addHours(2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Booking must be within operational hours (09:00 - 24:00).');

        $this->validator->execute($this->table->id, $start, $end);
    }

    public function test_fails_after_operational_hours(): void
    {
        $start = now('Asia/Jakarta')->addDays(2)->setTime(23, 0, 0);
        $end = $start->copy()->addHours(2); // Ends at 01:00 next day

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Booking must be within operational hours (09:00 - 24:00).');

        $this->validator->execute($this->table->id, $start, $end);
    }

    public function test_fails_beyond_maximum_days_in_advance(): void
    {
        $start = now('Asia/Jakarta')->addDays(15)->setTime(10, 0, 0);
        $end = $start->copy()->addHours(2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Booking cannot be made more than 14 days in advance.');

        $this->validator->execute($this->table->id, $start, $end);
    }

    public function test_online_booking_fails_within_one_hour_notice(): void
    {
        $this->travelTo(now('Asia/Jakarta')->setTime(10, 0, 0));

        $start = now('Asia/Jakarta')->setTime(10, 30, 0);
        $end = $start->copy()->addHours(2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Online bookings must be made at least 1 hour in advance.');

        $this->validator->execute($this->table->id, $start, $end, 'online');
    }

    public function test_walk_in_booking_passes_within_one_hour_notice(): void
    {
        $start = now('Asia/Jakarta')->addMinutes(5);
        if ($start->hour < 9) {
            $start->setTime(9, 5);
        } elseif ($start->hour >= 22) {
            $start->setTime(22, 0);
        }
        $end = $start->copy()->addHours(2);

        $result = $this->validator->execute($this->table->id, $start, $end, 'walk_in');
        $this->assertTrue($result);
    }

    public function test_fails_for_invalid_duration(): void
    {
        $start = now('Asia/Jakarta')->addDays(2)->setTime(10, 0, 0);

        // Too short (30 mins)
        $endShort = $start->copy()->addMinutes(30);
        try {
            $this->validator->execute($this->table->id, $start, $endShort);
            $this->fail('Expected exception for short duration');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Booking duration must be between', $e->getMessage());
        }

        // Too long (5 hours)
        $endLong = $start->copy()->addHours(5);
        try {
            $this->validator->execute($this->table->id, $start, $endLong);
            $this->fail('Expected exception for long duration');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Booking duration must be between', $e->getMessage());
        }
    }

    public function test_conflict_detection(): void
    {
        $start = now('Asia/Jakarta')->addDays(2)->setTime(12, 0, 0);
        $end = $start->copy()->addHours(2);

        Booking::factory()->create([
            'billiard_table_id' => $this->table->id,
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'confirmed',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The billiard table is already booked for the selected time slot.');
        $this->validator->execute($this->table->id, $start->copy()->subHour(), $start->copy()->addHour());
    }

    public function test_excludes_self_when_specified(): void
    {
        $start = now('Asia/Jakarta')->addDays(2)->setTime(12, 0, 0);
        $end = $start->copy()->addHours(2);

        $booking = Booking::factory()->create([
            'billiard_table_id' => $this->table->id,
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'confirmed',
        ]);

        $result = $this->validator->execute($this->table->id, $start, $end, 'online', $booking->id);
        $this->assertTrue($result);
    }
}
