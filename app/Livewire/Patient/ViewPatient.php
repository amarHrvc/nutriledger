<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ViewPatient extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    public function mount(Patient $patient): void
    {
        $this->authorize('view', $patient);
        $this->patient = $patient;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.patient.view-patient');
    }
}
