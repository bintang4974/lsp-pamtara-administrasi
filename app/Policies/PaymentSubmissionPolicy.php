<?php

namespace App\Policies;

use App\Models\PaymentSubmission;
use App\Models\User;

class PaymentSubmissionPolicy
{
    public function view(User $user, PaymentSubmission $submission): bool
    {
        return $user->isAdmin() || $submission->payment->registration->user_id === $user->id;
    }
}
