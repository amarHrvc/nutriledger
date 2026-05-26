<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SendDietPlanRequest;
use App\Http\Requests\StoreDietPlanRequest;
use App\Http\Requests\UpdateDietPlanRequest;
use App\Http\Resources\Api\DietPlanDeliveryResource;
use App\Http\Resources\Api\DietPlanResource;
use App\Http\Resources\Api\DietPlanSummaryResource;
use App\Jobs\GenerateDietPlanJob;
use App\Jobs\SendDietPlanEmailJob;
use App\Models\DietPlanDelivery;
use App\Models\Patient;
use App\Models\PatientDietPlan;
use Illuminate\Http\JsonResponse;

class DietPlanController extends ApiController
{
    public function store(StoreDietPlanRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('generate', [PatientDietPlan::class, $patient]);

        $plan = PatientDietPlan::create([
            'patient_id'   => $patient->id,
            'generated_by' => $request->user()->id,
            'status'       => 'pending',
        ]);

        GenerateDietPlanJob::dispatch($plan);

        return $this->success('Diet plan generation started.', 202, [
            'diet_plan' => [
                'id'         => $plan->id,
                'status'     => $plan->status,
                'created_at' => $plan->created_at,
            ],
        ]);
    }

    public function index(Patient $patient): JsonResponse
    {
        $this->authorize('viewAny', [PatientDietPlan::class, $patient]);

        $plans = $patient->dietPlans()
            ->with('doctor')
            ->latest()
            ->get();

        return $this->ok(
            'Diet plans retrieved successfully.',
            ['diet_plans' => DietPlanSummaryResource::collection($plans)]
        );
    }

    public function show(Patient $patient, PatientDietPlan $dietPlan): JsonResponse
    {
        if ($dietPlan->patient_id !== $patient->id) {
            abort(404);
        }

        $this->authorize('view', $dietPlan);

        $dietPlan->load(['doctor', 'editor']);

        return $this->ok('Diet plan retrieved successfully.', [
            'diet_plan' => new DietPlanResource($dietPlan),
        ]);
    }

    public function update(UpdateDietPlanRequest $request, Patient $patient, PatientDietPlan $dietPlan): JsonResponse
    {
        if ($dietPlan->patient_id !== $patient->id) {
            abort(404);
        }

        $this->authorize('update', $dietPlan);

        if ($dietPlan->status !== 'completed') {
            return $this->error('Diet plan must be completed to be edited.', 422);
        }

        $dietPlan->update([
            'daily_calories' => $request->input('daily_calories') ?? $dietPlan->daily_calories,
            'nutritional_goals' => $request->input('nutritional_goals') ?? $dietPlan->nutritional_goals,
            'days' => $request->input('days') ?? $dietPlan->days,
            'warnings' => $request->input('warnings') ?? $dietPlan->warnings,
            'rationale' => $request->input('rationale') ?? $dietPlan->rationale,
            'is_edited' => true,
            'edited_by' => $request->user()->id,
            'edited_at' => now(),
        ]);

        $dietPlan->load(['doctor', 'editor']);

        return $this->ok('Diet plan updated successfully.', [
            'diet_plan' => new DietPlanResource($dietPlan),
        ]);
    }

    public function send(SendDietPlanRequest $request, Patient $patient, PatientDietPlan $dietPlan): JsonResponse
    {
        if ($dietPlan->patient_id !== $patient->id) {
            abort(404);
        }

        $this->authorize('send', $dietPlan);

        if ($dietPlan->status !== 'completed') {
            return $this->error('Diet plan must be completed to be sent.', 422);
        }

        $patient->loadMissing('user');

        if (! $patient->user->email) {
            return $this->error('Patient email is not set.', 422);
        }

        $delivery = DietPlanDelivery::create([
            'diet_plan_id' => $dietPlan->id,
            'sent_by' => $request->user()->id,
            'recipient_email' => $patient->user->email,
            'status' => 'pending',
        ]);

        SendDietPlanEmailJob::dispatch($delivery);

        return $this->success('Diet plan delivery initiated.', 202, [
            'delivery' => new DietPlanDeliveryResource($delivery),
        ]);
    }
}
