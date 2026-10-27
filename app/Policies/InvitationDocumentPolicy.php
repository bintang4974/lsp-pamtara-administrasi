<?php

namespace App\Policies;

use App\Models\InvitationDocument;
use App\Models\User;

class InvitationDocumentPolicy
{
    public function view(User $user, InvitationDocument $invitation): bool
    {
        return $user->isAdmin() || $invitation->registration->user_id === $user->id;
    }
}
