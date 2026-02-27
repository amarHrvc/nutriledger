<?php

namespace App\Livewire\Patient\Socioeconomic;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ViewSocioeconomic extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    public ?PatientSocioeconomic $socioeconomic = null;

    public function mount(Patient $patient): void
    {
        $socioeconomic = PatientSocioeconomic::where('patient_id', $patient->id)->first();

        if ($socioeconomic) {
            $this->authorize('view', $socioeconomic);
        } else {
            $this->authorize('view', $patient);
        }

        $this->patient = $patient;
        $this->socioeconomic = $socioeconomic;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.patient.socioeconomic.view-socioeconomic', [
            'socioeconomic' => $this->socioeconomic,
        ]);
    }
}
