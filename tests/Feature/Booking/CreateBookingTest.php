<?php

namespace Tests\Feature\Booking;

use App\Actions\Booking\CreateBooking;
use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\PricingRule;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Tests\TestCase;

class CreateBookingTest extends TestCase
{
    use RefreshDatabase;

    private CreateBooking $action;

    private BilliardTable $table;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(CreateBooking::class);

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

        PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekday',
            'price_per_hour' => 15000,
            'effective_from' => '2026-07-01 00:00:00',
        ]);

        $this->table = BilliardTable::factory()->create(['is_active' => true]);
    }

    public function test_creates_booking_successfully_with_correct_code_price_and_history(): void
    {
        $this->travelTo(Carbon::parse('2026-07-15 10:00:00', 'Asia/Jakarta'));

        $startAt = Carbon::parse('2026-07-15 12:00:00', 'Asia/Jakarta');
        $endAt = Carbon::parse('2026-07-15 14:00:00', 'Asia/Jakarta');

        $user = User::factory()->create();

        $booking = $this->action->execute([
            'billiard_table_id' => $this->table->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '08123456789',
            'booking_type' => 'online',
            'start_at' => $startAt,
            'end_at' => $endAt,
        ], $user->id);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'customer_name' => 'John Doe',
            'total_price' => 30000,
            'status' => 'pending_payment',
            'expires_at' => Carbon::now()->addMinutes(15)->toDateTimeString(),
        ]);

        $this->assertMatchesRegularExpression('/^KSC-BL-260715-[A-Z0-9]{8}$/', $booking->booking_code);

        $this->assertDatabaseHas('booking_histories', [
            'booking_id' => $booking->id,
            'event_type' => 'booking_created',
            'new_status' => 'pending_payment',
            'actor_id' => $user->id,
        ]);
    }

    public function test_booking_creation_fails_on_conflict(): void
    {
        $startAt = Carbon::now('Asia/Jakarta')->addDays(2)->setTime(12, 0, 0);
        $endAt = $startAt->copy()->addHours(2);

        Booking::factory()->create([
            'billiard_table_id' => $this->table->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => 'confirmed',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The billiard table is already booked for the selected time slot.');

        $this->action->execute([
            'billiard_table_id' => $this->table->id,
            'customer_name' => 'Jane Doe',
            'customer_phone' => '08123456789',
            'booking_type' => 'online',
            'start_at' => $startAt->copy()->addHour(),
            'end_at' => $endAt->copy()->addHour(),
        ]);
    }
}
