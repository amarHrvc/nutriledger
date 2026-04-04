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
            'user_id' => ['required', 'integer', 'exists:users,id'],

            // Patient core fields
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:M,F'],
            'phone' => ['required', 'string', 'max:33'],
            'address' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:33'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'emergency_contact_name' => ['required', 'string', 'max:100'],
            'emergency_contact_phone' => ['required', 'string', 'max:50'],
            'blood_type' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],

            // Socioeconomic fields (all optional on create)
            'socioeconomic' => ['nullable', 'array'],
            'socioeconomic.marital_status' => ['nullable', 'in:single,married,divorced,widowed,separated,other'],
            'socioeconomic.number_of_dependents' => ['nullable', 'integer', 'min:0'],
            'socioeconomic.employment_status' => ['nullable', 'in:employed_full_time,employed_part_time,self_employed,unemployed,retired,student,unable_to_work,other'],
            'socioeconomic.income_level' => ['nullable', 'in:low,lower_middle,middle,upper_middle,high'],
            'socioeconomic.has_health_insurance' => ['nullable', 'boolean'],
            'socioeconomic.smoking_status' => ['nullable', 'in:never,former,current_light,current_heavy'],
            'socioeconomic.alcohol_consumption' => ['nullable', 'in:none,occasional,moderate,heavy'],
            'socioeconomic.physical_activity_level' => ['nullable', 'in:sedentary,lightly_active,moderately_active,very_active'],
            'socioeconomic.food_security_status' => ['nullable', 'in:food_secure,food_insecure,unsure'],
            'socioeconomic.additional_notes' => ['nullable', 'string'],
        ];
    }
}
