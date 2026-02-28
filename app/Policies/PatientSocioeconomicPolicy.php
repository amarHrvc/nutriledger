<?php

namespace App\Policies;

use App\Models\PatientSocioeconomic;
use App\Models\User;

class PatientSocioeconomicPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function view(User $user, PatientSocioeconomic $socioeconomic): bool
    {
        if ($user->isAdmin() || $user->isDoctor()) {
            return true;
        }

        return $user->isPatient() && $user->id === $socioeconomic->patient->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function update(User $user, PatientSocioeconomic $socioeconomic): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function delete(User $user, PatientSocioeconomic $socioeconomic): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
}
