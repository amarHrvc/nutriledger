<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSocioeconomicRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization is handled in the Livewire component.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'marital_status' => 'nullable|in:single,married,divorced,widowed,separated,other',
            'number_of_dependents' => 'nullable|integer|min:0|max:20',
            'living_arrangement' => 'nullable|in:alone,with_family,with_partner,shared_housing,care_facility,other',
            'employment_status' => 'nullable|in:employed_full_time,employed_part_time,self_employed,unemployed,retired,student,unable_to_work,other',
            'occupation' => 'nullable|string|max:255',
            'income_level' => 'nullable|in:low,lower_middle,middle,upper_middle,high',
            'has_health_insurance' => 'nullable|boolean',
            'education_level' => 'nullable|in:no_formal,primary,secondary,vocational,bachelors,masters,doctorate,other',
            'smoking_status' => 'nullable|in:never,former,current_light,current_heavy',
            'alcohol_consumption' => 'nullable|in:none,occasional,moderate,heavy',
            'physical_activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active',
            'has_family_support' => 'nullable|boolean',
            'has_caregiver' => 'nullable|boolean',
            'transportation_access' => 'nullable|in:own_vehicle,public_transport,rideshare,walking,limited,none',
            'food_security_status' => 'nullable|in:food_secure,marginally_secure,food_insecure,severely_insecure',
            'dietary_restrictions_cultural' => 'nullable|string|max:500',
            'additional_notes' => 'nullable|string|max:2000',
        ];
    }
}
