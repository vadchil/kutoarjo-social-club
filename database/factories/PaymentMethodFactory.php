<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'bank_transfer',
            'name' => fake()->word().' Bank',
            'bank_name' => fake()->word().' Bank',
            'account_number' => fake()->bankAccountNumber(),
            'account_holder' => fake()->name(),
            'instructions' => fake()->paragraph(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
