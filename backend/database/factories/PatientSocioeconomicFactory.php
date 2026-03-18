<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientSocioeconomic>
 */
class PatientSocioeconomicFactory extends Factory
{
    protected $model = PatientSocioeconomic::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),

            // Demographics & Social
            'marital_status' => fake()->optional(0.8)->randomElement(['single', 'married', 'divorced', 'widowed', 'partnered']),
            'number_of_dependents' => fake()->optional(0.7)->numberBetween(0, 6),
            'living_arrangement' => fake()->optional(0.8)->randomElement(['alone', 'with_family', 'with_partner', 'shared', 'institution']),

            // Economic
            'employment_status' => fake()->optional(0.8)->randomElement(['employed_full_time', 'employed_part_time', 'self_employed', 'unemployed', 'retired', 'student', 'disabled']),
            'occupation' => fake()->optional(0.7)->jobTitle(),
            'income_level' => fake()->optional(0.8)->randomElement(['low', 'middle', 'high', 'prefer_not_to_say']),
            'has_health_insurance' => fake()->boolean(70),

            // Lifestyle
            'education_level' => fake()->optional(0.8)->randomElement(['primary', 'secondary', 'vocational', 'bachelor', 'master', 'doctorate', 'other']),
            'smoking_status' => fake()->optional(0.8)->randomElement(['never', 'former', 'current']),
            'alcohol_consumption' => fake()->optional(0.8)->randomElement(['none', 'occasional', 'moderate', 'heavy']),
            'physical_activity_level' => fake()->optional(0.8)->randomElement(['sedentary', 'light', 'moderate', 'active', 'very_active']),

            // Support Systems
            'has_family_support' => fake()->boolean(75),
            'has_caregiver' => fake()->boolean(20),
            'transportation_access' => fake()->optional(0.8)->randomElement(['own_vehicle', 'public_transport', 'family', 'limited', 'none']),

            // Food Security
            'food_security_status' => fake()->optional(0.8)->randomElement(['secure', 'at_risk', 'insecure']),
            'dietary_restrictions_cultural' => fake()->optional(0.4)->sentence(),

            // Notes
            'additional_notes' => fake()->optional(0.3)->paragraph(),
        ];
    }
}
