<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientDietPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientDietPlan>
 */
class PatientDietPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id'   => Patient::factory(),
            'generated_by' => User::factory()->doctor(),
            'status'       => 'pending',
            'rationale'    => null,
            'daily_calories'     => null,
            'nutritional_goals'  => null,
            'days'               => null,
            'warnings'           => null,
            'failure_reason'     => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function completed(): static
    {
        return $this->state([
            'status'   => 'completed',
            'rationale' => $this->faker->paragraph(),
            'daily_calories' => $this->faker->numberBetween(1400, 2800),
            'nutritional_goals' => [
                'protein_g' => $this->faker->numberBetween(60, 150),
                'carbs_g'   => $this->faker->numberBetween(150, 300),
                'fat_g'     => $this->faker->numberBetween(40, 100),
            ],
            'days' => collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])
                ->map(fn (string $day) => [
                    'day'       => $day,
                    'breakfast' => $this->faker->sentence(4),
                    'lunch'     => $this->faker->sentence(4),
                    'dinner'    => $this->faker->sentence(4),
                    'snack'     => $this->faker->sentence(3),
                ])->all(),
            'warnings' => [$this->faker->sentence()],
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status'         => 'failed',
            'failure_reason' => $this->faker->sentence(),
        ]);
    }
}
