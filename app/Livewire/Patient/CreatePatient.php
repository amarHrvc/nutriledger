<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class CreatePatient extends Component
{
    // User fields
    public string $email = '';

    //Personal Information
    public string $first_name = '';
    public string $last_name = '';
    public string $gender = '';
    public string $date_of_birth = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $postal_code = '';
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $blood_type = '';
    public string $allergies = '';
    public string $medical_notes = '';


    public function render()
    {
        return view('livewire.patient.create-patient');
    }

    protected function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:M,F',
            'phone' => 'string|max:33',
            'address' => 'nullable|string|max:99',
            'city' => 'nullable|string|max:33',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'string|max:99',
            'emergency_contact_phone' => 'string|max:99',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'medical_notes' => 'nullable|string',
        ];
    }

    public function createPatient()
    {
        $validated = $this->validate();
        DB::transaction(function () use($validated) {
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $this->email,
                'role' => 'pacijent',
                'password' => Hash::make(Str::random(12)), // Temporary password
            ]);

            Patient::create([
                'user_id' => $user->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'emergency_contact_name' => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'blood_type' => $this->blood_type,
                'allergies' => $this->allergies,
                'medical_notes' => $this->medical_notes

            ]);
        });

        session()->flash('success', 'Patient created successfully.');

        return $this->redirect(route('patients.index'));

    }
}
