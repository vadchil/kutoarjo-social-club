<?php

namespace Database\Factories;

use App\Models\PricingRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PricingRule>
 */
class PricingRuleFactory extends Factory
{
    protected $model = PricingRule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'activity_type' => 'billiard',
            'day_type' => 'weekday',
            'price_per_hour' => 15000,
            'effective_from' => now(),
            'effective_until' => null,
            'is_active' => true,
        ];
    }
}
