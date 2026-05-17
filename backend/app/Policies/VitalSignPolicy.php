<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VitalSign;

class VitalSignPolicy
{
    public function view(User $user, VitalSign $vitalSign): bool
    {
        if ($user->isAdmin() || $user->isDoctor()) {
            return true;
        }

        if ($user->isPatient()) {
            return $user->patient?->id === $vitalSign->visit->patient_id;
        }

        return false;
    }

    public function create(User $user, Visit $visit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->id === $visit->doctor_id;
        }

        return false;
    }

    public function update(User $user, VitalSign $vitalSign): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->id === $vitalSign->visit->doctor_id;
        }

        return false;
    }

    public function delete(User $user, VitalSign $vitalSign): bool
    {
        return $user->isAdmin();
    }

    public function viewHistory(User $user, Patient $patient): bool
    {
        if ($user->isAdmin() || $user->isDoctor()) {
            return true;
        }

        if ($user->isPatient()) {
            return $user->patient?->id === $patient->id;
        }

        return false;
    }
}
