<?php

namespace App\Policies;

use App\Models\DocumentSubmission;
use App\Models\User;

class DocumentSubmissionPolicy
{
    public function view(User $user, DocumentSubmission $submission): bool
    {
        return $user->isAdmin() || $submission->registrationDocument->registration->user_id === $user->id;
    }
}
