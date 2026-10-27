<?php

return [
    // Disk privat untuk seluruh dokumen peserta, bukti bayar, dan PDF undangan.
    'private_disk' => 'local',

    'uploads' => [
        'payment_proof' => [
            'allowed_mime_types' => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_kb' => 2048,
        ],
    ],
];
