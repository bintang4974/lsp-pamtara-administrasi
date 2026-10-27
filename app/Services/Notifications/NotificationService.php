<?php

namespace App\Services\Notifications;

use App\Models\AppNotification;
use App\Models\Registration;
use App\Models\User;

/**
 * Notifikasi dalam aplikasi saja (lihat DATABASE.md §6). Kanal eksternal (email/WA) opsional
 * dan terpisah; jangan klaim pesan terkirim tanpa hasil pengiriman valid (lihat PRD.md §5.9).
 */
class NotificationService
{
    public function notify(
        User $user,
        string $title,
        string $message,
        string $type,
        ?Registration $registration = null,
    ): AppNotification {
        return AppNotification::create([
            'user_id' => $user->id,
            'registration_id' => $registration?->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]);
    }
}
