<?php

namespace Database\Factories;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => fake()->sentence(6).'?',
            'answer' => fake()->paragraph(),
            'category' => fake()->randomElement(['general', 'padel', 'billiard']),
            'sort_order' => 0,
            'is_published' => true,
            'created_by' => User::factory(),
        ];
    }
}
