<?php

namespace App\Livewire\Patient\Visit;

use App\Http\Requests\StoreVisitRequest;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Livewire\Component;
use Livewire\Form;
use Illuminate\Support\Facades\Log;

class CreateVisit extends Component
{

    use AuthorizesRequests;

    public Patient $patient;


    /** @var array<int,array{id:int,name:string}> */
    public array $doctors = [];


    /** @var array{date:?string,notes:?string,doctor_id:?int} */
    public array $form = [
        'date' => null,
        'notes' => null,
        'doctor_id' => null,
    ];

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
        $this->authorize('create', Visit::class);

        $user = auth()->user();
        if ($user !== null && $user->isDoctor()) {
            $this->form['doctor_id'] = $user->id;
        } else {
//             for admin or other roles, provide dovtor list to select from
            $this->doctors = \App\Models\User::where('role', 'doktor')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? "Doctor #{$user->id}"),
                    ];
                })
                ->toArray();
        }

    }

    public function save(): Redirector
    {

        $validated = $this->validate([
            'form.date'      => ['required', 'date'],
            'form.notes'     => ['nullable', 'string', 'max:500'],
            'form.doctor_id' => ['nullable', 'exists:users,id'],
        ]);

        $data = $validated['form'];

        if (auth()->user() !== null && auth()->user()->isDoctor()) {
            $data['doctor_id'] = auth()->id();
        } else {
            $data['doctor_id'] = $this->form['doctor_id'] ?? null;
        }

        $visit = Visit::query()->create([
            'patient_id' => $this->patient->id,
            'doctor_id'  => $data['doctor_id'],
            'date'       => $data['date'],
            'notes'      => $data['notes'] ?? null,
        ]);

        session()->flash('message', 'Visit created successfully.');
        return redirect()->route('visit.show', [$this->patient, $visit]);
    }


    public function render()
    {
        return view('livewire.patient.visit.create-visit');
    }
}
