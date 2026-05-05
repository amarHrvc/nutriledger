<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Http\Resources\Api\VisitResource;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;

class VisitController extends ApiController
{
    public function globalIndex(): JsonResponse
    {
        $this->authorize('globalIndex', Visit::class);

        $today = now()->toDateString();

        $visits = Visit::with(['doctor', 'patient.user'])
            ->when(auth()->user()->isDoctor(), function ($query) {
                $query->where('doctor_id', auth()->id());
            })
            ->orderByRaw("CASE WHEN date >= '{$today}' THEN 0 ELSE 1 END")
            ->orderBy('date')
            ->orderBy('time')
            ->paginate();

        return $this->paginated(
            'Visits retrieved successfully.',
            VisitResource::collection($visits)
        );
    }

    public function index(Patient $patient): JsonResponse
    {
        $this->authorize('viewAny', [Visit::class, $patient]);

        $visits = $patient->visits()
            ->with('doctor')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate();

        return $this->paginated(
            'Visit history retrieved successfully.',
            VisitResource::collection($visits)
        );
    }

    public function show(Patient $patient, Visit $visit): JsonResponse
    {
        // Ensure visit belongs to this patient (route scoping)
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }

        $this->authorize('view', $visit);

        $visit->load(['doctor', 'patient.user']);

        return $this->ok('Visit retrieved successfully.', [
            'visit' => new VisitResource($visit),
        ]);
    }

    public function store(StoreVisitRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('create', Visit::class);

        $doctorId = (auth()->user()->isAdmin() && $request->filled('doctor_id'))
            ? (int) $request->doctor_id
            : auth()->id();

        $visit = $patient->visits()->create([
            'date' => $request->date,
            'time' => $request->time,
            'notes' => $request->notes,
            'doctor_id' => $doctorId,
        ]);

        return $this->created('Visit created successfully.', [
            'visit' => new VisitResource($visit->load(['patient', 'doctor'])),
        ]);
    }

    public function update(UpdateVisitRequest $request, Patient $patient, Visit $visit): JsonResponse
    {
        // Ensure visit belongs to this patient (route scoping)
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }

        $visit->update($request->validated());

        return $this->ok('Visit updated successfully.', [
            'visit' => new VisitResource($visit->load(['doctor', 'patient.user'])),
        ]);
    }

    public function destroy(Patient $patient, Visit $visit): JsonResponse
    {
        // Ensure visit belongs to this patient (route scoping)
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }

        $this->authorize('delete', $visit);

        $visit->delete();

        return $this->noContent();
    }
}
