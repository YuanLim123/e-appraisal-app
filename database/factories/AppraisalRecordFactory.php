<?php

namespace Database\Factories;

use App\Enums\AppraisalRecordPurposeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppraisalRecord>
 */
class AppraisalRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 years', 'now');
        $end = fake()->dateTimeBetween($start, '+1 month');

        return [
            'review_from' => $start->format('Y-m-d'),
            'review_to' => $end->format('Y-m-d'),
            'purpose' => fake()->randomElement(AppraisalRecordPurposeType::cases()),
            'total' => fake()->numberBetween(50, 100),
            'performance' => null,
            'section_percentage' => null,
        ];
    }

    /**
     * Indicate that the record is supervision type
     */
    public function supervision(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'performance' => [
                    [
                        'goal' => fake()->sentence(10),
                        'result' => fake()->sentence(15),
                        'rating' => fake()->numberBetween(10, 90),
                    ],
                ],
                'section_percentage' => [
                    fake()->numberBetween(10, 70),
                    fake()->numberBetween(10, 70),
                ],
            ];
        });
    }
}
