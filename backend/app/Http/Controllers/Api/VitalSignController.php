<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreVitalSignRequest;
use App\Http\Requests\UpdateVitalSignRequest;
use App\Http\Resources\Api\VitalSignResource;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\VitalSign;
use App\Services\VitalSignService;
use Illuminate\Http\JsonResponse;

class VitalSignController extends ApiController
{
    public function __construct(private readonly VitalSignService $vitalSignService) {}

    public function show(Patient $patient, Visit $visit): JsonResponse
    {
        $this->scopeVisitToPatient($visit, $patient);

        $vitalSign = $visit->vitalSign;

        if ($vitalSign === null) {
            abort(404);
        }

        $this->authorize('view', $vitalSign);

        $vitalSign->load(['visit.patient.user', 'visit.doctor']);
        $vitalSign->previousVitals = $this->loadPreviousVitals($visit);

        return $this->ok(
            'Vital signs retrieved.',
            (new VitalSignResource($vitalSign))->toArray(request())
        );
    }

    public function store(StoreVitalSignRequest $request, Patient $patient, Visit $visit): JsonResponse
    {
        $this->scopeVisitToPatient($visit, $patient);

        if ($visit->vitalSign()->exists()) {
            return $this->error('Vital signs already recorded for this visit.', 409);
        }

        $vitalSign = $this->vitalSignService->record($visit, $request->validated());

        $vitalSign->load(['visit.patient.user', 'visit.doctor']);

        return $this->created(
            'Vital signs recorded.',
            (new VitalSignResource($vitalSign))->toArray(request())
        );
    }

    public function update(UpdateVitalSignRequest $request, Patient $patient, Visit $visit): JsonResponse
    {
        $this->scopeVisitToPatient($visit, $patient);

        $vitalSign = $visit->vitalSign;

        if ($vitalSign === null) {
            abort(404);
        }

        $vitalSign = $this->vitalSignService->update($vitalSign, $request->validated());

        $vitalSign->load(['visit.patient.user', 'visit.doctor']);

        return $this->ok(
            'Vital signs updated.',
            (new VitalSignResource($vitalSign))->toArray(request())
        );
    }

    public function destroy(Patient $patient, Visit $visit): JsonResponse
    {
        $this->scopeVisitToPatient($visit, $patient);

        $vitalSign = $visit->vitalSign;

        if ($vitalSign === null) {
            abort(404);
        }

        $this->authorize('delete', $vitalSign);
        $this->vitalSignService->delete($vitalSign);

        return $this->noContent();
    }

    public function history(Patient $patient): JsonResponse
    {
        $this->authorize('viewHistory', [VitalSign::class, $patient]);

        $query = VitalSign::whereHas('visit', fn ($q) => $q->where('patient_id', $patient->id))
            ->with(['visit.patient.user', 'visit.doctor'])
            ->orderByDesc(Visit::select('date')->whereColumn('visits.id', 'vital_signs.visit_id'));

        if ($from = request()->query('from')) {
            $query->whereHas('visit', fn ($q) => $q->where('date', '>=', $from));
        }

        if ($to = request()->query('to')) {
            $query->whereHas('visit', fn ($q) => $q->where('date', '<=', $to));
        }

        $vitals = $query->paginate(request()->integer('per_page', 15));

        return $this->paginated(
            'Vitals history retrieved.',
            VitalSignResource::collection($vitals)
        );
    }

    private function scopeVisitToPatient(Visit $visit, Patient $patient): void
    {
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }
    }

    private function loadPreviousVitals(Visit $currentVisit): ?VitalSign
    {
        return VitalSign::whereHas('visit', fn ($q) => $q
            ->where('patient_id', $currentVisit->patient_id)
            ->where('date', '<', $currentVisit->date))
            ->orderByDesc(Visit::select('date')->whereColumn('visits.id', 'vital_signs.visit_id'))
            ->with(['visit'])
            ->first();
    }
}
