<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $status
 * @property-read string|null $rationale
 * @property-read int|null $daily_calories
 * @property-read array|null $nutritional_goals
 * @property-read array|null $days
 * @property-read array|null $warnings
 * @property-read string|null $failure_reason
 * @property-read \Carbon\Carbon $created_at
 * @property-read \App\Models\User|null $doctor
 */
class DietPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'status'           => $this->status,
            'rationale'        => $this->rationale,
            'dailyCalories'    => $this->daily_calories,
            'nutritionalGoals' => $this->nutritional_goals,
            'days'             => $this->days,
            'warnings'         => $this->warnings,
            'failureReason'    => $this->failure_reason,
            'generatedBy'      => new UserResource($this->whenLoaded('doctor')),
            'createdAt'        => $this->created_at->toDateTimeString(),
        ];
    }
}
