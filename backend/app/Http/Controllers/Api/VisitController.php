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

        $visit->load('doctor');

        return $this->ok('Visit retrieved successfully.', [
            'visit' => new VisitResource($visit),
        ]);
    }

    public function store(StoreVisitRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('create', Visit::class);

        $visit = $patient->visits()->create([
            'date' => $request->date,
            'notes' => $request->notes,
            'doctor_id' => auth()->id(),
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
            'visit' => new VisitResource($visit->load(['patient', 'doctor'])),
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
