<?php

namespace App\Http\Requests;

use App\Models\Patient;
use App\Models\PatientDietPlan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDietPlanRequest extends FormRequest
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

        return $this->user()->can('update', $dietPlan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rationale'           => ['sometimes', 'nullable', 'string'],
            'daily_calories'      => ['sometimes', 'integer', 'between:1000,4000'],
            'nutritional_goals'   => ['sometimes', 'nullable', 'array'],
            'days'                => ['sometimes', 'nullable', 'array', 'size:7'],
            'days.*.day'          => ['required_with:days', 'string'],
            'days.*.breakfast'    => ['required_with:days', 'string'],
            'days.*.lunch'        => ['required_with:days', 'string'],
            'days.*.dinner'       => ['required_with:days', 'string'],
            'days.*.snack'        => ['required_with:days', 'string'],
            'warnings'            => ['sometimes', 'nullable', 'array'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'days.size' => 'A diet plan must contain exactly 7 days.',
        ];
    }
}
