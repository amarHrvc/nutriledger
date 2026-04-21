<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

class VisitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Patient $patient): bool
    {
        // Admin or doctor can view any patient's visits
        if ($this->adminOrDoctor($user)) {
            return true;
        }

        // Patients can only view their own visits
        if ($user->isPatient()) {
            return $user->patient?->id === $patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Visit $visit): bool
    {
        if ($this->adminOrDoctor($user)) {
            return true;
        }

        // Patients can view their own visits
        if ($user->isPatient()) {
            return $user->patient?->id === $visit->patient_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admins and doctors can create visits
        return $this->adminOrDoctor($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Visit $visit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDoctor() && $user->id === $visit->doctor_id;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Visit $visit): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Visit $visit): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Visit $visit): bool
    {
        return $user->isAdmin();
    }

    public function adminOrDoctor(User $user): bool
    {
        if ($user->isAdmin() || $user->isDoctor()) {
            return true;
        }

        return false;
    }
}
