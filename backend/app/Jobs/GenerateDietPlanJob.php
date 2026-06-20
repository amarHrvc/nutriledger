<?php

namespace App\Jobs;

use App\Ai\Agents\DietPlanAgent;
use App\Models\PatientDietPlan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Validator;
use Throwable;

class GenerateDietPlanJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public PatientDietPlan $plan) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            /** @var \App\Models\Patient $patient */
            $patient = $this->plan->patient()->with('socioeconomic')->firstOrFail();
            $lastError = null;

            for($attempt = 1; $attempt <= 2; $attempt++) {
                $response = (new DietPlanAgent($patient))->prompt('Generate the plan.');

                $validator = Validator::make(json_decode($response->text, true) ?? [], [
                    'rationale'                   => ['required', 'string'],
                    'daily_calories'              => ['required', 'integer', 'between:1000,4000'],
                    'nutritional_goals'           => ['required', 'array'],
                    'nutritional_goals.protein_g' => ['required', 'integer', 'min:0'],
                    'nutritional_goals.carbs_g'   => ['required', 'integer', 'min:0'],
                    'nutritional_goals.fat_g'     => ['required', 'integer', 'min:0'],
                    'days'                        => ['required', 'array', 'size:7'],
                    'days.*.day'                  => ['required', 'string'],
                    'days.*.breakfast'            => ['required', 'string'],
                    'days.*.lunch'                => ['required', 'string'],
                    'days.*.dinner'               => ['required', 'string'],
                    'days.*.snack'                => ['required', 'string'],
                    'warnings'                    => ['required', 'array'],
                    'warnings.*'                  => ['string'],
                ]);

                if ($validator->fails()) {
                    $lastError = $validator->errors()->first();
                    continue;
                }

                $validated = $validator->validated();

                $this->plan->update([
                    'status'             => 'completed',
                    'rationale'          => $validated['rationale'],
                    'daily_calories'     => $validated['daily_calories'],
                    'nutritional_goals'  => $validated['nutritional_goals'],
                    'days'               => $validated['days'],
                    'warnings'           => $validated['warnings'],
                ]);

                return;

            }
            // Both attempts failed validation
            $this->plan->update([
                'status'         => 'failed',
                'failure_reason' => "AI response failed validation after 2 attempts: {$lastError}",
            ]);



        } catch (Throwable $e) {
            $this->plan->update([
                'status'         => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);
        }
    }
}
