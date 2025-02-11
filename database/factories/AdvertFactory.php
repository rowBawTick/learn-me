<?php

namespace Database\Factories;

use App\Models\Advert;
use App\Models\Currency;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advert>
 */
class AdvertFactory extends Factory
{
    protected $model = Advert::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currency = Currency::where('code', 'GBP')->first() ?? Currency::inRandomOrder()->first();

        return [
            'user_id' => User::factory(),
            'subject_id' => Subject::factory(),
            'currency_id' => $currency->id,
            'title' => 'Placeholder Title', // This is replaced in configure()
            'description' => fake()->paragraph() . "\n\n" .
                "Experience:\n" . fake()->randomElement([
                    "- Over 5 years of teaching experience\n",
                    "- Certified instructor\n",
                    "- Native speaker\n",
                    "- University degree in the subject\n"
                ]) .
                "- Personalized lesson plans\n" .
                "- Flexible scheduling\n",
            'price_per_hour' => fake()->randomFloat(2, 20, 100),
            'is_active' => true,
        ];
    }

    /**
     * Configure the factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Advert $advert) {
            $subject = Subject::find($advert->subject_id);
            $advert->title = fake()->randomElement([
                "Experienced {$subject->name} Tutor",
                "Learn {$subject->name} with a Professional",
                "{$subject->name} Lessons for All Levels",
                "Private {$subject->name} Tutoring",
                "Expert {$subject->name} Teacher"
            ]);
        });
    }
}
