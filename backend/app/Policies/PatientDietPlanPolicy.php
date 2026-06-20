<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\PatientDietPlan;
use App\Models\User;

class PatientDietPlanPolicy
{
    public function generate(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function viewAny(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function view(User $user, PatientDietPlan $dietPlan): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
}
