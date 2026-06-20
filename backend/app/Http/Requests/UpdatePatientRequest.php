<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Patient $patient */
        $patient = $this->route('patient');

        return $this->user()->can('update', $patient);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:50'],
            'last_name' => ['sometimes', 'string', 'max:50'],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'gender' => ['sometimes', 'in:M,F'],
            'phone' => ['sometimes', 'string', 'max:33'],
            'address' => ['sometimes', 'nullable', 'string', 'max:100'],
            'city' => ['sometimes', 'nullable', 'string', 'max:33'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'emergency_contact_name' => ['sometimes', 'string', 'max:100'],
            'emergency_contact_phone' => ['sometimes', 'string', 'max:50'],
            'blood_type' => ['sometimes', 'nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'allergies' => ['sometimes', 'nullable', 'string'],
            'medical_notes' => ['sometimes', 'nullable', 'string'],

            'user_id' => ['prohibited'],

            'socioeconomic' => ['sometimes', 'nullable', 'array'],
            'socioeconomic.marital_status' => ['sometimes', 'nullable', 'in:single,married,divorced,widowed,separated,other'],
            'socioeconomic.number_of_dependents' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:20'],
            'socioeconomic.living_arrangement' => ['sometimes', 'nullable', 'in:alone,with_family,with_partner,shared_housing,care_facility,other'],
            'socioeconomic.employment_status' => ['sometimes', 'nullable', 'in:employed_full_time,employed_part_time,self_employed,unemployed,retired,student,unable_to_work,other'],
            'socioeconomic.occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'socioeconomic.income_level' => ['sometimes', 'nullable', 'in:low,lower_middle,middle,upper_middle,high'],
            'socioeconomic.has_health_insurance' => ['sometimes', 'nullable', 'boolean'],
            'socioeconomic.education_level' => ['sometimes', 'nullable', 'in:no_formal,primary,secondary,vocational,bachelors,masters,doctorate,other'],
            'socioeconomic.smoking_status' => ['sometimes', 'nullable', 'in:never,former,current_light,current_heavy'],
            'socioeconomic.alcohol_consumption' => ['sometimes', 'nullable', 'in:none,occasional,moderate,heavy'],
            'socioeconomic.physical_activity_level' => ['sometimes', 'nullable', 'in:sedentary,lightly_active,moderately_active,very_active'],
            'socioeconomic.has_family_support' => ['sometimes', 'nullable', 'boolean'],
            'socioeconomic.has_caregiver' => ['sometimes', 'nullable', 'boolean'],
            'socioeconomic.transportation_access' => ['sometimes', 'nullable', 'in:own_vehicle,public_transport,rideshare,walking,limited,none'],
            'socioeconomic.food_security_status' => ['sometimes', 'nullable', 'in:food_secure,marginally_secure,food_insecure,severely_insecure'],
            'socioeconomic.dietary_restrictions_cultural' => ['sometimes', 'nullable', 'string', 'max:500'],
            'socioeconomic.additional_notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
