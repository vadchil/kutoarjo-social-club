<?php

namespace Tests\Feature\Booking;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\PaymentMethod;
use App\Models\PricingRule;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublicBookingControllerTest extends TestCase
{
    use RefreshDatabase;

    private BilliardTable $table;

    private PaymentMethod $pm;

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

        $this->table = BilliardTable::factory()->create(['is_active' => true]);
        $this->pm = PaymentMethod::factory()->create(['is_active' => true]);

        PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekday',
            'price_per_hour' => 15000,
            'effective_from' => '2026-07-01 00:00:00',
        ]);
        PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekend',
            'price_per_hour' => 20000,
            'effective_from' => '2026-07-01 00:00:00',
        ]);
    }

    public function test_booking_create_page_renders(): void
    {
        $response = $this->get(route('bookings.create'));
        $response->assertStatus(200);
    }

    public function test_can_check_availability(): void
    {
        $response = $this->postJson(route('bookings.check'), [
            'billiard_table_id' => $this->table->id,
            'start_at' => '2026-07-20 10:00:00',
            'end_at' => '2026-07-20 12:00:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'available' => true,
        ]);
    }

    public function test_can_store_public_booking(): void
    {
        $this->travelTo(Carbon::parse('2026-07-15 10:00:00', 'Asia/Jakarta'));

        $response = $this->post(route('bookings.store'), [
            'billiard_table_id' => $this->table->id,
            'payment_method_id' => $this->pm->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '08123456789',
            'booking_type' => 'online',
            'start_at' => '2026-07-15 12:00:00',
            'end_at' => '2026-07-15 14:00:00',
        ]);

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $response->assertRedirect(route('bookings.success', $booking->booking_code));
    }

    public function test_can_search_booking_status(): void
    {
        $booking = Booking::factory()->create([
            'billiard_table_id' => $this->table->id,
            'payment_method_id' => $this->pm->id,
            'customer_phone' => '08123456789',
            'booking_code' => 'KSC-BL-260715-TEST',
            'start_at' => '2026-07-15 12:00:00',
            'end_at' => '2026-07-15 14:00:00',
        ]);

        $response = $this->post(route('bookings.search'), [
            'booking_code' => 'KSC-BL-260715-TEST',
            'customer_phone' => '08123456789',
        ]);

        $response->assertStatus(200);
        $response->assertSee('KSC-BL-260715-TEST');
    }
}
