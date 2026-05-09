<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string|null $marital_status
 * @property-read int $number_of_dependents
 * @property-read string|null $living_arrangement
 * @property-read string|null $employment_status
 * @property-read string|null $occupation
 * @property-read string|null $income_level
 * @property-read bool $has_health_insurance
 * @property-read string|null $education_level
 * @property-read string|null $smoking_status
 * @property-read string|null $alcohol_consumption
 * @property-read string|null $physical_activity_level
 * @property-read bool $has_family_support
 * @property-read bool $has_caregiver
 * @property-read string|null $transportation_access
 * @property-read string|null $food_security_status
 * @property-read string|null $dietary_restrictions_cultural
 * @property-read string|null $additional_notes
 * @property-read ?Carbon $created_at
 * @property-read ?Carbon $updated_at
 */
class PatientSocioeconomicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'patient_socioeconomic',
            'id' => (string) $this->id,
            'attributes' => [
                'maritalStatus' => $this->marital_status,
                'numberOfDependents' => $this->number_of_dependents,
                'livingArrangement' => $this->living_arrangement,
                'employmentStatus' => $this->employment_status,
                'occupation' => $this->occupation,
                'incomeLevel' => $this->income_level,
                'hasHealthInsurance' => $this->has_health_insurance,
                'educationLevel' => $this->education_level,
                'smokingStatus' => $this->smoking_status,
                'alcoholConsumption' => $this->alcohol_consumption,
                'physicalActivityLevel' => $this->physical_activity_level,
                'hasFamilySupport' => $this->has_family_support,
                'hasCaregiver' => $this->has_caregiver,
                'transportationAccess' => $this->transportation_access,
                'foodSecurityStatus' => $this->food_security_status,
                'dietaryRestrictionsCultural' => $this->dietary_restrictions_cultural,
                'additionalNotes' => $this->additional_notes,
                'createdAt' => $this->created_at?->toIso8601String(),
                'updatedAt' => $this->updated_at?->toIso8601String(),
            ],
        ];
    }
}
