<?php

namespace App\Http\Resources\Api;

use App\Models\PatientSocioeconomic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id;
 * @property-read string|null $first_name;
 * @property-read string|null $last_name;
 * @property-read string|null $full_name;
 * @property-read ?Carbon $date_of_birth;
 * @property-read string|null $gender;
 * @property-read string|null $phone;
 * @property-read string|null $address;
 * @property-read string|null $city;
 * @property-read string|null $postal_code;
 * @property-read string|null $emergency_contact_name;
 * @property-read string|null $emergency_contact_phone;
 * @property-read string|null $blood_type;
 * @property-read string|null $allergies;
 * @property-read string|null $medical_notes;
 * @property-read ?Carbon $created_at;
 * @property-read ?Carbon $updated_at;
 * @property-read PatientSocioeconomic|null $socioeconomic;
 * @property-read User|null $user;
 **/
class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'patient',
            'id' => (string) $this->id,
            'attributes' => [
                'firstName' => $this->first_name,
                'lastName' => $this->last_name,
                'fullName' => $this->full_name,
                'dateOfBirth' => $this->date_of_birth?->toDateString(),
                'gender' => $this->gender,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'postalCode' => $this->postal_code,
                'emergencyContactName' => $this->emergency_contact_name,
                'emergencyContactPhone' => $this->emergency_contact_phone,
                'bloodType' => $this->blood_type,
                'allergies' => $this->allergies,
                'medicalNotes' => $this->medical_notes,
                'createdAt' => $this->created_at?->toIso8601String(),
                'updatedAt' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'user' => [
                    'data' => $this->whenLoaded('user',
                        fn () => [
                            'type' => 'user',
                            'id' => (string) $this->user->id,
                        ]),
                ],
                'socioeconomic' => [
                    'data' => $this->whenLoaded('socioeconomic', fn () => $this->socioeconomic
                        ? [
                            'type' => 'patient_socioeconomic',
                            'id' => (string) $this->socioeconomic->id,
                        ] : null),
                ],
            ],
        ];
    }
}
