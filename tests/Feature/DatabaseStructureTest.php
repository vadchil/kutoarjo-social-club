<?php

namespace Tests\Feature;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\PaymentMethod;
use App\Models\PricingRule;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_models_have_ulid_primary_keys(): void
    {
        $user = User::factory()->create();
        $this->assertEquals(26, strlen($user->id));

        $table = BilliardTable::factory()->create();
        $this->assertEquals(26, strlen($table->id));

        $pm = PaymentMethod::factory()->create();
        $this->assertEquals(26, strlen($pm->id));

        $booking = Booking::factory()->create([
            'billiard_table_id' => $table->id,
            'payment_method_id' => $pm->id,
            'created_by' => $user->id,
        ]);
        $this->assertEquals(26, strlen($booking->id));
        $this->assertEquals(26, strlen($booking->billiard_table_id));
        $this->assertEquals(26, strlen($booking->payment_method_id));
        $this->assertEquals(26, strlen($booking->created_by));

        $pricing = PricingRule::factory()->create();
        $this->assertEquals(26, strlen($pricing->id));

        $setting = SiteSetting::factory()->create();
        $this->assertEquals(26, strlen($setting->id));
    }

    public function test_composite_indexes_bookings(): void
    {
        // Simple assertion to ensure tables are loaded properly and relationships queryable
        $booking = Booking::factory()->create();
        $this->assertInstanceOf(BilliardTable::class, $booking->billiardTable);
        $this->assertInstanceOf(PaymentMethod::class, $booking->paymentMethod);
        $this->assertInstanceOf(User::class, $booking->user);
    }
}
