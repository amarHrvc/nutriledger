<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Patient::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Link to existing patient-role user (required)
            'userId' => ['required', 'integer', 'exists:users,id'],

            // Patient core fields
            'firstName' => ['required', 'string', 'max:50'],
            'lastName' => ['required', 'string', 'max:50'],
            'dateOfBirth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:M,F'],
            'phone' => ['required', 'string', 'max:33'],
            'address' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:33'],
            'postalCode' => ['nullable', 'string', 'max:20'],
            'emergencyContactName' => ['required', 'string', 'max:100'],
            'emergencyContactPhone' => ['required', 'string', 'max:50'],
            'bloodType' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'allergies' => ['nullable', 'string'],
            'medicalNotes' => ['nullable', 'string'],

            // Socioeconomic fields (all optional on create)
            'socioeconomic' => ['nullable', 'array'],
            'socioeconomic.maritalStatus' => ['nullable', 'in:single,married,divorced,widowed,separated, other'],
            'socioeconomic.numberOfDependents' => ['nullable', 'integer', 'min:0'],
            'socioeconomic.employmentStatus' => ['nullable', 'in:employed_full_time,employed_part_time, self_employed,unemployed,retired,student,unable_to_work,other'],
            'socioeconomic.incomeLevel' => ['nullable', 'in:low,lower_middle,middle,upper_middle,high'],
            'socioeconomic.hasHealthInsurance' => ['nullable', 'boolean'],
            'socioeconomic.smokingStatus' => ['nullable', 'in:never,former,current_light,current_heavy'],
            'socioeconomic.alcoholConsumption' => ['nullable', 'in:none,occasional,moderate,heavy'],
            'socioeconomic.physicalActivityLevel' => ['nullable', 'in:sedentary,lightly_active, moderately_active,very_active'],
            'socioeconomic.foodSecurityStatus' => ['nullable', 'in:food_secure,food_insecure,unsure'],
            'socioeconomic.additionalNotes' => ['nullable', 'string'],
        ];
    }
}
