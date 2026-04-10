<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Resources\Api\VisitResource;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;

class VisitController extends ApiController
{
    public function store(StoreVisitRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('create', Visit::class);

        $visit = $patient->visits()->create([
            'date' => $request->date,
            'notes' => $request->notes,
            'doctor_id' => auth()->id(),
        ]);

        return response()->json(
            new VisitResource($visit->load(['patient', 'doctor'])),
            201
        );
    }

    public function update(StoreVisitRequest $request, Patient $patient, Visit $visit): JsonResponse
    {
        $this->authorize('update', $visit);

        $visit->update([
            'date' => $request->date,
            'notes' => $request->notes,
        ]);

        return response()->json(
            new VisitResource($visit->load(['patient', 'doctor']))
        );
    }
}
