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

    public function mount(Patient $patient): void
    {
        $socioeconomic = PatientSocioeconomic::where('patient_id', $patient->id)->first();

        if ($socioeconomic) {
            $this->authorize('update', $socioeconomic);
        }

        $this->patient = $patient;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.patient.socioeconomic.manage-socioeconomic');
    }
}
