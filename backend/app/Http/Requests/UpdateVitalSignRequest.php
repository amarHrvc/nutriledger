<?php

namespace App\Http\Requests;

use App\Models\Visit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVitalSignRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Visit $visit */
        $visit = $this->route('visit');
        $vitalSign = $visit->vitalSign;

        if ($vitalSign === null) {
            return true; // controller handles the 404
        }

        return $this->user() !== null && $this->user()->can('update', $vitalSign);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'systolic_bp' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:350'],
            'diastolic_bp' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:250'],
            'heart_rate' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:350'],
            'temperature' => ['sometimes', 'nullable', 'numeric', 'min:30.0', 'max:45.0'],
            'weight' => ['sometimes', 'nullable', 'numeric', 'min:1.0', 'max:500.0'],
            'height' => ['sometimes', 'nullable', 'numeric', 'min:50.0', 'max:300.0'],
        ];
    }
}
