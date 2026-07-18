<?php

namespace Database\Factories;

use App\Models\BilliardTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BilliardTable>
 */
class BilliardTableFactory extends Factory
{
    protected $model = BilliardTable::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'table_number' => fake()->unique()->numberBetween(1, 100),
            'name' => 'Billiard Table '.fake()->unique()->numberBetween(1, 100),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
