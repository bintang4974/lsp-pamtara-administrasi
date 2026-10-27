<?php

namespace App\Enums;

/**
 * Dipakai bersama untuk registration_documents.current_status (missing|under_review|approved|revision_required)
 * dan document_submissions.status / payment_submissions.status (submitted|under_review|approved|revision_required).
 * Setiap tabel hanya memakai subset case yang relevan; validasi subset di service masing-masing.
 */
enum DocumentStatus: string
{
    case Missing = 'missing';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case RevisionRequired = 'revision_required';

    public function label(): string
    {
        return match ($this) {
            self::Missing => 'Belum diunggah',
            self::Submitted => 'Terkirim',
            self::UnderReview => 'Menunggu pemeriksaan',
            self::Approved => 'Disetujui',
            self::RevisionRequired => 'Perlu revisi',
        };
    }
}
