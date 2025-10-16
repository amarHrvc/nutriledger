<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class PatientList extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public $perPage = 10;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deletePatient(int $patientId): void
    {
        $patient = Patient::findOrFail($patientId);
        $this->authorize('delete', $patient);
        $patient->delete();
        session()->flash('success', 'Patient deleted successfully.');
        $this->dispatch('patient-deleted');
    }
    public function render()
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('email', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.patient.patient-list', ['patients' => $patients]);
    }
}
