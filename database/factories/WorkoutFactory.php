<?php

namespace Database\Factories;

use App\Enums\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{
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
