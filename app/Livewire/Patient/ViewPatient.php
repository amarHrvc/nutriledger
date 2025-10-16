<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ViewPatient extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    public ?PatientSocioeconomic $socioeconomic = null;

    public function mount(Patient $patient): void
    {
        $this->authorize('view', $patient);
        $this->patient = $patient;
        $this->socioeconomic = $patient->socioeconomic;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.patient.view-patient', [
            'socioeconomic' => $this->socioeconomic,
        ]);
    }
}
