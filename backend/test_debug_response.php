<?php

use App\Models\{Patient, PatientDietPlan, User};
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DebugResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_patch_response()
    {
        $patient = Patient::factory()->create();
        $admin = User::factory()->admin()->create();
        $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create();

        $response = $this->actingAs($admin)
            ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                'daily_calories' => 2000,
            ]);

        dd([
            'status' => $response->status(),
            'json' => $response->json(),
            'data_keys' => array_keys($response->json('data') ?? []),
        ]);
    }
}
