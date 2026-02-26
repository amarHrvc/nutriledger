<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class EditPatient extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    public string $first_name = '';
    public string $last_name = '';
    public string $date_of_birth = '';
    public string $gender = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $postal_code = '';
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $blood_type = '';
    public string $allergies = '';
    public string $medical_notes = '';

    public function mount(Patient $patient): void
    {
        $this->authorize('update', $patient);
        $this->patient = $patient;
        $this->first_name = $patient->first_name;
        $this->last_name = $patient->last_name;
        $this->date_of_birth = $patient->date_of_birth?->format('Y-m-d') ?? '';
        $this->gender = $patient->gender ?? '';
        $this->phone = $patient->phone ?? '';
        $this->address = $patient->address ?? '';
        $this->city = $patient->city ?? '';
        $this->postal_code = $patient->postal_code ?? '';
        $this->emergency_contact_name = $patient->emergency_contact_name ?? '';
        $this->emergency_contact_phone = $patient->emergency_contact_phone ?? '';
        $this->blood_type = $patient->blood_type ?? '';
        $this->allergies = $patient->allergies ?? '';
        $this->medical_notes = $patient->medical_notes ?? '';
    }

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:M,F',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'medical_notes' => 'nullable|string',
        ];
    }

    public function updatePatient(): mixed
    {
        $validated = $this->validate();

        $this->patient->update($validated);

        session()->flash('success', 'Patient updated successfully.');

        return $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.patient.edit-patient');
    }
}
