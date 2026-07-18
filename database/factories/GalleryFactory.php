<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'category' => fake()->randomElement(['padel', 'billiard', 'venue']),
            'image_path' => 'galleries/'.fake()->uuid().'.jpg',
            'alt_text' => fake()->sentence(5),
            'sort_order' => 0,
            'is_published' => true,
            'created_by' => User::factory(),
        ];
    }
}
