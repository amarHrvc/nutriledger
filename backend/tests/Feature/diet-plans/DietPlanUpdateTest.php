<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\PatientDietPlan;
use App\Models\User;

describe('DietPlanUpdateTest', function () {
    describe('authorization', function () {
        it('guest cannot PATCH diet plan', function () {
            $patient = Patient::factory()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create();

            $response = $this->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                'daily_calories' => 2000,
            ]);

            $response->assertUnauthorized();
        });

        it('patient cannot PATCH diet plan', function () {
            $patient = Patient::factory()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create();

            $response = $this->actingAs($patient->user)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 2000,
                ]);

            $response->assertForbidden();
        });

        it('doctor can PATCH completed diet plan', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 2000,
                ]);

            $response->assertOk();
            expect($response->json('data.diet_plan.isEdited'))->toBeTrue();
            expect($response->json('data.diet_plan.editedBy.attributes.name'))->toBe($doctor->name);
        });

        it('admin can PATCH diet plan', function () {
            $patient = Patient::factory()->create();
            $admin = User::factory()->admin()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create();

            $response = $this->actingAs($admin)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 2000,
                ]);

            $response->assertOk();
        });
    });

    describe('route scoping', function () {
        it('returns 404 when diet plan belongs to different patient', function () {
            $patient1 = Patient::factory()->create();
            $patient2 = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient2)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->patchJson("/api/patients/{$patient1->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 2000,
                ]);

            $response->assertNotFound();
        });
    });

    describe('validation', function () {
        it('cannot PATCH pending diet plan', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->create([
                'status' => 'pending',
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 2000,
                ]);

            $response->assertUnprocessable();
        });

        it('cannot PATCH failed diet plan', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->create([
                'status' => 'failed',
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 2000,
                ]);

            $response->assertUnprocessable();
        });

        it('rejects invalid daily_calories', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 500,
                ]);

            $response->assertUnprocessable();
        });

        it('rejects days with 6 or fewer elements', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'days' => array_fill(0, 6, ['day' => 'Monday']),
                ]);

            $response->assertUnprocessable();
        });
    });

    describe('database state', function () {
        it('sets is_edited to true after PATCH', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
                'is_edited' => false,
            ]);

            $this->actingAs($doctor)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 2000,
                ]);

            $dietPlan->refresh();
            expect($dietPlan->is_edited)->toBeTrue();
            expect($dietPlan->edited_by)->toBe($doctor->id);
            expect($dietPlan->edited_at)->not->toBeNull();
        });
    });

    describe('response structure', function () {
        it('includes edited metadata in response', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->patchJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}", [
                    'daily_calories' => 2000,
                ]);

            $response->assertJsonStructure([
                'message',
                'status',
                'data' => [
                    'diet_plan' => [
                        'id',
                        'status',
                        'isEdited',
                        'editedBy' => [
                           'type',
                           'id',
                           'attributes' => [
                               'name',
                           ],
                       ],
                       'editedAt',
                   ],
               ],
            ]);
        });
    });
});
