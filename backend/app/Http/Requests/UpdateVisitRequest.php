<?php

namespace App\Http\Requests;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Visit $visit */
        $visit = $this->route('visit');
        /** @var Patient $patient */
        $patient = $this->route('patient');

        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }

        return $this->user()->can('update', $visit);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['sometimes', 'date', 'before_or_equal:today'],
            'time' => ['sometimes', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
