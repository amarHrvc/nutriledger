<?php

namespace Database\Factories;

use App\Models\Visit;
use App\Models\VitalSign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VitalSign>
 */
class VitalSignFactory extends Factory
{
    public function definition(): array
    {
        $weight = $this->faker->randomFloat(2, 45, 120);
        $height = $this->faker->randomFloat(2, 150, 200);
        $bmi = round($weight / ($height / 100) ** 2, 2);

        return [
            'visit_id' => Visit::factory(),
            'systolic_bp' => $this->faker->numberBetween(90, 140),
            'diastolic_bp' => $this->faker->numberBetween(60, 90),
            'heart_rate' => $this->faker->numberBetween(50, 100),
            'temperature' => $this->faker->randomFloat(1, 36.0, 37.5),
            'weight' => $weight,
            'height' => $height,
            'bmi' => $bmi,
        ];
    }
}
