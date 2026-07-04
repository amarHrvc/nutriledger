<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
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
     * Create a new patient-role login account together with its patient record,
     * in one transaction. The account role is always "pacijent" — it is never
     * taken from caller input, so this method cannot be used to mint doctor
     * or admin accounts.
     *
     * @throws \Throwable
     */
    public function createPatientWithAccount(array $userData, array $patientData, ?array $socioeconomicData = null): Patient
    {
        return DB::transaction(function () use ($userData, $patientData, $socioeconomicData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'role' => 'pacijent',
            ]);

            $patientData['user_id'] = $user->id;

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
