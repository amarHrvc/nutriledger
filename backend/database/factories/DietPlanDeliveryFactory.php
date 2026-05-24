<?php

namespace Database\Factories;

use App\Models\DietPlanDelivery;
use App\Models\PatientDietPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DietPlanDelivery>
 */
class DietPlanDeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'diet_plan_id'  => PatientDietPlan::factory(),
            'sent_by'       => User::factory()->doctor(),
            'recipient_email' => $this->faker->safeEmail(),
            'status'        => 'pending',
            'failure_reason' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(['status' => 'sent']);
    }

    public function failed(): static
    {
        return $this->state([
            'status'         => 'failed',
            'failure_reason' => 'SMTP timeout',
        ]);
    }
}
