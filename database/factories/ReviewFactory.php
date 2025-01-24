<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Advert;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'advert_id' => Advert::factory(),
            'reviewer_id' => User::factory()->state(['is_tutor' => false]),
            'rating' => $this->faker->numberBetween(1, 5),
            'description' => $this->faker->realText(200),
        ];
    }

    /**
     * Indicate that the review is positive.
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(4, 5),
            'description' => $this->faker->randomElement([
                'Excellent tutor! Very knowledgeable and patient.',
                'Great teaching style. Really helped me understand the subject.',
                'Highly recommended! Clear explanations and good communication.',
                'Very professional and well-prepared for each session.',
                'Fantastic experience! Will definitely book more sessions.',
            ]),
        ]);
    }

    /**
     * Indicate that the review is negative.
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
            'description' => $this->faker->randomElement([
                'Not very helpful. Explanations were unclear.',
                'Often late to sessions and seemed unprepared.',
                'Did not meet my expectations.',
                'Communication was poor.',
                'Would not recommend.',
            ]),
        ]);
    }

    /**
     * Indicate that the review is moderate.
     */
    public function moderate(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 3,
            'description' => $this->faker->randomElement([
                'Decent tutor. Could improve communication.',
                'OK experience. Some concepts were well explained.',
                'Average teaching style but got the job done.',
                'Fair value for money.',
                'Reasonable experience overall.',
            ]),
        ]);
    }
}
