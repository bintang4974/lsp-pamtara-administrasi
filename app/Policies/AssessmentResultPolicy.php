<?php

namespace App\Policies;

use App\Models\AssessmentResult;
use App\Models\User;

class AssessmentResultPolicy
{
    public function view(User $user, AssessmentResult $result): bool
    {
        return $user->isAdmin() || $result->registration->user_id === $user->id;
    }
}
