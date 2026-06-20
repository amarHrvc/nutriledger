<?php

namespace App\Http\Requests;

use App\Models\Patient;
use App\Models\PatientDietPlan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SendDietPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var PatientDietPlan $dietPlan */
        $dietPlan = $this->route('dietPlan');
        /** @var Patient $patient */
        $patient = $this->route('patient');

        if ($dietPlan->patient_id !== $patient->id) {
            abort(404);
        }

        return $this->user()->can('send', $dietPlan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
