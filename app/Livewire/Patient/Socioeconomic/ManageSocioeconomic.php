<?php

namespace App\Livewire\Patient\Socioeconomic;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ManageSocioeconomic extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    public ?PatientSocioeconomic $socioeconomic = null;

    public bool $isEditing = false;

    public ?string $marital_status = null;

    public ?int $number_of_dependents = null;

    public ?string $living_arrangement = null;

    public ?string $employment_status = null;

    public ?string $occupation = null;

    public ?string $income_level = null;

    public bool $has_health_insurance = false;

    public ?string $education_level = null;

    public ?string $smoking_status = null;

    public ?string $alcohol_consumption = null;

    public ?string $physical_activity_level = null;

    public bool $has_family_support = false;

    public bool $has_caregiver = false;

    public ?string $transportation_access = null;

    public ?string $food_security_status = null;

    public ?string $dietary_restrictions_cultural = null;

    public ?string $additional_notes = null;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
        $this->socioeconomic = $patient->socioeconomic;

        if ($this->socioeconomic) {
            $this->isEditing = true;
            $this->authorize('update', $this->socioeconomic);

            foreach ($this->socioeconomic->toArray() as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        } else {
            $this->authorize('create', PatientSocioeconomic::class);
        }
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
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

    public function save(): mixed
    {
        $validated = $this->validate();

        if ($this->isEditing) {
            $this->socioeconomic->update($validated);
        } else {
            $this->patient->socioeconomic()->create($validated);
        }

        session()->flash('success', $this->isEditing ? 'Socioeconomic data updated.' : 'Socioeconomic data saved.');

        return $this->redirect(route('patients.socioeconomic.show', $this->patient), navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.patient.socioeconomic.manage-socioeconomic');
    }
}
