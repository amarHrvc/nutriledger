<?php

namespace App\Livewire\Patient\Socioeconomic;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class DeleteSocioeconomic extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    public PatientSocioeconomic $socioeconomic;

    public function mount(Patient $patient): void
    {
        $socioeconomic = $patient->socioeconomic;

        if (! $socioeconomic) {
            abort(404, 'No socioeconomic data found for this patient.');
        }

        $this->authorize('delete', $socioeconomic);
        $this->patient = $patient;
        $this->socioeconomic = $socioeconomic;
    }

    public function delete(): mixed
    {
        $this->authorize('delete', $this->socioeconomic);
        $this->socioeconomic->delete();

        session()->flash('success', 'Socioeconomic data deleted successfully.');

        return $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.patient.socioeconomic.delete-socioeconomic');
    }
}
