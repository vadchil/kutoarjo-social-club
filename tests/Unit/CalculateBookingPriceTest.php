<?php

namespace Tests\Unit;

use App\Actions\CalculateBookingPrice;
use App\Models\PricingRule;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateBookingPriceTest extends TestCase
{
    use RefreshDatabase;

    private CalculateBookingPrice $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CalculateBookingPrice;

        SiteSetting::factory()->create([
            'key' => 'timezone',
            'value' => ['name' => 'Timezone', 'value' => 'Asia/Jakarta'],
        ]);
    }

    public function test_calculates_weekday_billiard_pricing(): void
    {
        PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekday',
            'price_per_hour' => 15000,
            'effective_from' => '2026-07-01 00:00:00',
        ]);

        $result = $this->calculator->execute('billiard', '2026-07-15 10:00:00', '2026-07-15 12:00:00');

        $this->assertEquals(30000, $result['total_price']);
        $this->assertEquals(120, $result['duration_minutes']);
        $this->assertEquals(15000, $result['hourly_price']);
    }

    public function test_calculates_weekend_billiard_pricing(): void
    {
        PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekend',
            'price_per_hour' => 20000,
            'effective_from' => '2026-07-01 00:00:00',
        ]);

        $result = $this->calculator->execute('billiard', '2026-07-18 14:00:00', '2026-07-18 16:00:00');

        $this->assertEquals(40000, $result['total_price']);
        $this->assertEquals(120, $result['duration_minutes']);
        $this->assertEquals(20000, $result['hourly_price']);
    }

    public function test_calculates_boundary_crossing_weekday_to_weekend(): void
    {
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

        $result = $this->calculator->execute('billiard', '2026-07-17 23:00:00', '2026-07-18 01:00:00');

        $this->assertEquals(35000, $result['total_price']);
        $this->assertEquals(120, $result['duration_minutes']);
    }

    public function test_calculates_boundary_crossing_weekend_to_weekday(): void
    {
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

        $result = $this->calculator->execute('billiard', '2026-07-19 23:00:00', '2026-07-20 01:00:00');

        $this->assertEquals(35000, $result['total_price']);
        $this->assertEquals(120, $result['duration_minutes']);
    }

    public function test_calculates_effective_date_transition_on_same_day_type(): void
    {
        $r1 = PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekday',
            'price_per_hour' => 15000,
            'effective_from' => '2026-07-01 00:00:00',
            'effective_until' => '2026-07-17 12:00:00',
        ]);

        $r2 = PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekday',
            'price_per_hour' => 18000,
            'effective_from' => '2026-07-17 12:00:00',
        ]);

        $result = $this->calculator->execute('billiard', '2026-07-17 11:00:00', '2026-07-17 13:00:00');

        $this->assertEquals(33000, $result['total_price']);
    }

    public function test_calculates_padel_pricing(): void
    {
        PricingRule::factory()->create([
            'activity_type' => 'padel',
            'day_type' => 'weekday',
            'price_per_hour' => 160000,
            'effective_from' => '2026-07-01 00:00:00',
        ]);

        PricingRule::factory()->create([
            'activity_type' => 'padel',
            'day_type' => 'weekend',
            'price_per_hour' => 200000,
            'effective_from' => '2026-07-01 00:00:00',
        ]);

        $result = $this->calculator->execute('padel', '2026-07-18 14:00:00', '2026-07-18 17:00:00');

        $this->assertEquals(600000, $result['total_price']);
    }
}
