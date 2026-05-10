<?php

namespace App\Http\Resources\Api;

use App\Models\VitalSign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VitalSign
 */
class VitalSignResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'type' => 'vital_sign',
            'attributes' => [
                'systolicBp' => $this->systolic_bp,
                'diastolicBp' => $this->diastolic_bp,
                'heartRate' => $this->heart_rate,
                'temperature' => $this->temperature,
                'weight' => $this->weight,
                'height' => $this->height,
                'bmi' => $this->bmi,
                'bmiCategory' => $this->bmiCategory(),
                'flags' => $this->computed_flags,
                'visitId' => (string) $this->visit_id,
                'visitDate' => $this->whenLoaded('visit', fn () => $this->visit->date->toDateString()),
                'patientId' => $this->whenLoaded('visit', fn () => $this->visit->relationLoaded('patient')
                    ? (string) $this->visit->patient->id
                    : null),
                'patientName' => $this->whenLoaded('visit', fn () => $this->visit->relationLoaded('patient')
                    ? $this->visit->patient->user->name
                    : null),
                'doctorName' => $this->whenLoaded('visit', fn () => $this->visit->relationLoaded('doctor')
                    ? $this->visit->doctor->name
                    : null),
                ...($this->resource->previousVitals !== null ? ['previousVisit' => $this->buildPreviousVisit()] : []),
                'createdAt' => $this->created_at?->toIso8601String(),
                'updatedAt' => $this->updated_at?->toIso8601String(),
            ],
        ];
    }

    private function bmiCategory(): ?string
    {
        if ($this->bmi === null) {
            return null;
        }

        $bmi = (float) $this->bmi;

        if ($bmi < 18.5) {
            return 'underweight';
        }

        if ($bmi < 25.0) {
            return 'normal';
        }

        if ($bmi < 30.0) {
            return 'overweight';
        }

        return 'obese';
    }

    /** @return array<string, mixed>|null */
    private function buildPreviousVisit(): ?array
    {
        /** @var VitalSign|null $prev */
        $prev = $this->resource->previousVitals;

        if ($prev === null) {
            return null;
        }

        $weightDelta = ($this->weight !== null && $prev->weight !== null)
            ? round((float) $this->weight - (float) $prev->weight, 2)
            : null;

        $bmiDelta = ($this->bmi !== null && $prev->bmi !== null)
            ? round((float) $this->bmi - (float) $prev->bmi, 2)
            : null;

        return [
            'visitDate' => $prev->visit->date->toDateString(),
            'weight' => $prev->weight,
            'bmi' => $prev->bmi,
            'systolicBp' => $prev->systolic_bp,
            'diastolicBp' => $prev->diastolic_bp,
            'weightDelta' => $weightDelta,
            'bmiDelta' => $bmiDelta,
        ];
    }
}
