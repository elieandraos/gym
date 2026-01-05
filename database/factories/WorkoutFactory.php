<?php

namespace Database\Factories;

use App\Enums\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{
    /**
     * Provide default attribute values for a Workout model factory.
     *
     * Returns an associative array with:
     * - `name`: a single random word used as the workout name.
     * - `categories`: an array of 1 to 3 Category enum values (as scalars).
     * - `image`: a random image URL sized 60x60.
     *
     * @return array{name: string, categories: string[], image: string}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'categories' => array_map(
                fn ($cat) => $cat->value,
                $this->faker->randomElements(array: Category::cases(), count: $this->faker->numberBetween(1, 3))
            ),
            'image' => $this->faker->imageUrl(width: 60, height: 60),
        ];
    }
}
