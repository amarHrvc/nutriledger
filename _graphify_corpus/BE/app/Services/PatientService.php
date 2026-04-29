<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class PatientService
{
    /**
     * @throws \Throwable
     */
    public function createPatient(array $patientData, ?array $socioeconomicData = null): Patient
    {
        return DB::transaction(function () use ($patientData, $socioeconomicData) {
            $patient = Patient::create($patientData);
            if ($socioeconomicData) {
                $patient->socioeconomic()->create($socioeconomicData);
            }

            return $patient->load('socioeconomic', 'user');
        });
    }

    /**
     * @throws \Throwable
     */
    public function updatePatient(Patient $patient, array $patientData, ?array $socioeconomicData = null): Patient
    {
        return DB::transaction(function () use ($patient, $patientData, $socioeconomicData) {
            $patient->update($patientData);
            if ($socioeconomicData) {
                $patient->socioeconomic()->updateOrCreate(['patient_id' => $patient->id], $socioeconomicData);
            }

            return $patient->fresh(['socioeconomic', 'user']);
        });
    }
}
