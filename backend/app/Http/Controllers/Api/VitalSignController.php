<?php

namespace App\Http\Controllers\Api;

use App\Models\Patient;
use App\Models\VitalSign;
use App\Models\Visit;
use App\Services\VitalSignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VitalSignController extends ApiController
{
    public function __construct(private readonly VitalSignService $vitalSignService) {}

    public function show(Patient $patient, Visit $visit): JsonResponse
    {
        abort(501);
    }

    public function store(Request $request, Patient $patient, Visit $visit): JsonResponse
    {
        abort(501);
    }

    public function update(Request $request, Patient $patient, Visit $visit): JsonResponse
    {
        abort(501);
    }

    public function destroy(Patient $patient, Visit $visit): JsonResponse
    {
        abort(501);
    }

    public function history(Patient $patient): JsonResponse
    {
        abort(501);
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
