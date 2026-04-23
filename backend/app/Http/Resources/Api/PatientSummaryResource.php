<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string|null $full_name
 * @property-read ?Carbon $date_of_birth
 * @property-read string|null $gender
 * @property-read string|null $city
 * @property-read string|null $phone
 **/
class PatientSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'fullName' => $this->full_name,
            'dateOfBirth' => $this->date_of_birth?->toDateString(),
            'gender' => $this->gender,
            'city' => $this->city,
            'phone' => $this->phone,
        ];
    }
}
