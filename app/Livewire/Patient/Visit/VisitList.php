<?php

namespace App\Livewire\Patient\Visit;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class VisitList extends Component
{
    use AuthorizesRequests;

    public $patient;

    public function mount(Patient $patient):void {
        $this->authorize('viewAny', Visit::class);
    }


    public function render()
    {
        return view('livewire.patient.visit.visit-list', [
            'visits' => $this->patient->visits()->with('doctor')->get(),
        ]);
    }

}
