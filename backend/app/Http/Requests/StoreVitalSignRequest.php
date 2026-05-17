<?php

namespace App\Http\Requests;

use App\Models\Visit;
use App\Models\VitalSign;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreVitalSignRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Visit $visit */
        $visit = $this->route('visit');

        return $this->user() !== null && $this->user()->can('create', [VitalSign::class, $visit]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'systolic_bp' => ['nullable', 'integer', 'min:1', 'max:350'],
            'diastolic_bp' => ['nullable', 'integer', 'min:1', 'max:250'],
            'heart_rate' => ['nullable', 'integer', 'min:1', 'max:350'],
            'temperature' => ['nullable', 'numeric', 'min:30.0', 'max:45.0'],
            'weight' => ['nullable', 'numeric', 'min:1.0', 'max:500.0'],
            'height' => ['nullable', 'numeric', 'min:50.0', 'max:300.0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $fields = ['systolic_bp', 'diastolic_bp', 'heart_rate', 'temperature', 'weight', 'height'];
            $allNull = collect($fields)->every(fn ($f) => $this->input($f) === null);

            if ($allNull) {
                $v->errors()->add('vitals', 'At least one vital sign measurement must be provided.');
            }
        });
    }
}
