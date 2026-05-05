<?php

namespace App\Http\Requests;

use App\Models\Visit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVisitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('create', Visit::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'doctor_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'doktor')],
        ];
    }
}
