<?php

namespace Tests\Feature\Booking;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ExpireBookingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    public function test_expires_past_pending_bookings(): void
    {
        $now = Carbon::parse('2026-07-18 10:00:00');
        Carbon::setTestNow($now);

        $table = BilliardTable::factory()->create(['is_active' => true]);

        // Should expire
        $expiredBooking = Booking::factory()->create([
            'billiard_table_id' => $table->id,
            'status' => 'pending_payment',
            'expires_at' => $now->copy()->subMinute(),
        ]);

        // Should not expire (future)
        $futureBooking = Booking::factory()->create([
            'billiard_table_id' => $table->id,
            'status' => 'pending_payment',
            'expires_at' => $now->copy()->addMinute(),
        ]);

        // Should not expire (different status)
        $confirmedBooking = Booking::factory()->create([
            'billiard_table_id' => $table->id,
            'status' => 'confirmed',
            'expires_at' => $now->copy()->subMinute(),
        ]);

        Artisan::call('bookings:expire');

        $this->assertDatabaseHas('bookings', [
            'id' => $expiredBooking->id,
            'status' => 'expired',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $futureBooking->id,
            'status' => 'pending_payment',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $confirmedBooking->id,
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('booking_histories', [
            'booking_id' => $expiredBooking->id,
            'event_type' => 'status_expired',
            'previous_status' => 'pending_payment',
            'new_status' => 'expired',
            'actor_type' => 'system',
            'actor_id' => null,
        ]);
    }

    public function test_command_is_scheduled(): void
    {
        $schedule = app(Schedule::class);

        $events = collect($schedule->events())->filter(function ($event) {
            return str_contains($event->command, 'bookings:expire');
        });

        $this->assertCount(1, $events);

        $event = $events->first();
        $this->assertEquals('* * * * *', $event->expression);
        $this->assertTrue($event->withoutOverlapping);
    }
}
