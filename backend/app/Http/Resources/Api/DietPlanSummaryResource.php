<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $status
 * @property-read int|null $daily_calories
 * @property-read array|null $nutritional_goals
 * @property-read array|null $warnings
 * @property-read string|null $failure_reason
 * @property-read \Carbon\Carbon $created_at
 * @property-read bool $is_edited
 * @property-read \App\Models\User|null $doctor
 */
class DietPlanSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'status'           => $this->status,
            'dailyCalories'    => $this->daily_calories,
            'nutritionalGoals' => $this->nutritional_goals,
            'warnings'         => $this->warnings,
            'failureReason'    => $this->failure_reason,
            'isEdited'         => $this->is_edited,
            'generatedBy'      => new UserResource($this->whenLoaded('doctor')),
            'createdAt'        => $this->created_at->toDateTimeString(),
        ];
    }
}
