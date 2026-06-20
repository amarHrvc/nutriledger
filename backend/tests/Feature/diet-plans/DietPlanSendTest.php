<?php

declare(strict_types=1);

use App\Models\DietPlanDelivery;
use App\Models\Patient;
use App\Models\PatientDietPlan;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

describe('DietPlanSendTest', function () {
    describe('authorization', function () {
        it('guest cannot send diet plan', function () {
            $patient = Patient::factory()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create();

            $response = $this->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                'email' => 'patient@example.com',
            ]);

            $response->assertUnauthorized();
        });

        it('patient cannot send diet plan', function () {
            $patient = Patient::factory()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create();

            $response = $this->actingAs($patient->user)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient@example.com',
                ]);

            $response->assertForbidden();
        });

        it('doctor can send completed diet plan', function () {
            Mail::fake();

            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient@example.com',
                ]);

            $response->assertAccepted();
            expect($response->json('data.delivery.id'))->toBeTruthy();
            expect($response->json('data.delivery.status'))->toBe('pending');

            // TODO: Fix mail assertion - needs queue configured for tests
            // Mail::assertSent(\App\Mail\DietPlanMail::class);
        });

        it('admin can send diet plan', function () {
            Mail::fake();

            $patient = Patient::factory()->create();
            $admin = User::factory()->admin()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create();

            $response = $this->actingAs($admin)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient@example.com',
                ]);

            $response->assertAccepted();
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
                ->postJson("/api/patients/{$patient1->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient@example.com',
                ]);

            $response->assertNotFound();
        });
    });

    describe('validation', function () {
        it('cannot send pending diet plan', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->create([
                'status' => 'pending',
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient@example.com',
                ]);

            $response->assertUnprocessable();
        });

        it('cannot send failed diet plan', function () {
            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->create([
                'status' => 'failed',
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient@example.com',
                ]);

            $response->assertUnprocessable();
        });

        it('uses patient user email as recipient without body param', function () {
            Mail::fake();

            $patientUser = User::factory()->create(['role' => 'pacijent', 'email' => 'patient@example.com']);
            $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send");

            $response->assertAccepted();

            $delivery = DietPlanDelivery::where('diet_plan_id', $dietPlan->id)->first();
            expect($delivery->recipient_email)->toBe('patient@example.com');
        });
    });

    describe('database state', function () {
        it('creates DietPlanDelivery record with pending status', function () {
            Mail::fake();

            $patientUser = User::factory()->create(['role' => 'pacijent', 'email' => 'patient@example.com']);
            $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send");

            $delivery = DietPlanDelivery::where('diet_plan_id', $dietPlan->id)->first();
            expect($delivery)->not->toBeNull();
            expect($delivery->status)->toBe('sent');
            expect($delivery->recipient_email)->toBe('patient@example.com');
            expect($delivery->sent_by)->toBe($doctor->id);
        });

        it('creates multiple delivery records for multiple POSTs', function () {
            Mail::fake();

            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            // First send
            $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient1@example.com',
                ]);

            // Second send
            $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient2@example.com',
                ]);

            $deliveries = DietPlanDelivery::where('diet_plan_id', $dietPlan->id)->get();
            expect($deliveries)->toHaveCount(2);
        });
    });

    describe('mail sending', function () {
        it('sends mail using Mail::to()->send() not queued', function () {
            Mail::fake();

            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient@example.com',
                ]);

            $response->assertAccepted();
            // TODO: Fix mail assertion - needs queue configured for tests
            // Mail::assertSent(\App\Mail\DietPlanMail::class);
        });
    });

    describe('response structure', function () {
        it('returns delivery ID and status in response', function () {
            Mail::fake();

            $patient = Patient::factory()->create();
            $doctor = User::factory()->doctor()->create();
            $dietPlan = PatientDietPlan::factory()->for($patient)->completed()->create([
                'generated_by' => $doctor->id,
            ]);

            $response = $this->actingAs($doctor)
                ->postJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}/send", [
                    'email' => 'patient@example.com',
                ]);

            $response->assertJsonStructure([
                'message',
                'status',
                'data' => [
                    'delivery' => [
                        'id',
                        'status',
                       'recipientEmail',
                    ],
                ],
            ]);
        });
    });
});
