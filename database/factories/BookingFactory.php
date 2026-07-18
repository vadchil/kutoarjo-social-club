<?php

namespace Database\Factories;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startAt = fake()->dateTimeBetween('now', '+14 days');
        $durationHours = fake()->numberBetween(1, 4);
        $endAt = (clone $startAt)->modify("+{$durationHours} hours");

        return [
            'booking_code' => 'KSC-BL-'.now()->format('ymd').'-'.strtoupper(Str::random(4)),
            'billiard_table_id' => BilliardTable::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'booking_type' => fake()->randomElement(['online', 'walk_in', 'admin_manual']),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'duration_minutes' => $durationHours * 60,
            'hourly_price' => 15000,
            'total_price' => $durationHours * 15000,
            'status' => 'pending_payment',
            'payment_status' => 'unpaid',
            'expires_at' => now()->addMinutes(15),
            'created_by' => User::factory(),
        ];
    }
}
