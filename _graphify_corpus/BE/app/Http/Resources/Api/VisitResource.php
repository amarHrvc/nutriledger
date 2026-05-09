<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read int $patient_id
 * @property-read int $doctor_id
 * @property-read Carbon $date
 * @property-read string|null $notes
 * @property-read ?Carbon $created_at
 * @property-read ?Carbon $updated_at
 * @property-read object|null $patient
 * @property-read object|null $doctor
 **/
class VisitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'visit',
            'id' => (string) $this->id,
            'attributes' => [
                'date' => $this->date->toDateString(),
                'notes' => $this->notes,
                'doctorName' => $this->whenLoaded('doctor', fn () => $this->doctor->name),
                'createdAt' => $this->created_at?->toIso8601String(),
                'updatedAt' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'patient' => [
                    'data' => $this->whenLoaded('patient',
                        fn () => [
                            'type' => 'patient',
                            'id' => (string) $this->patient->id,
                        ]),
                ],
                'doctor' => [
                    'data' => $this->whenLoaded('doctor',
                        fn () => [
                            'type' => 'user',
                            'id' => (string) $this->doctor->id,
                        ]),
                ],
            ],
        ];
    }
}
