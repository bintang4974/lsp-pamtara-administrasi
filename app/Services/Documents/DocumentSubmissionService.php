<?php

namespace App\Services\Documents;

use App\Enums\DocumentStatus;
use App\Enums\RegistrationStatus;
use App\Models\DocumentSubmission;
use App\Models\RegistrationDocument;
use App\Models\User;
use App\Services\Registration\RegistrationStatusService;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Unggah versi baru dokumen ke disk privat. Validasi MIME (server, finfo) dan ukuran dari
 * SNAPSHOT persyaratan (bukan master terkini). File lama tidak pernah ditimpa (lihat
 * ARCHITECTURE.md §6, DATABASE.md §3).
 */
class DocumentSubmissionService
{
    private const EXTENSION_BY_MIME = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public function __construct(private RegistrationStatusService $statusService) {}

    /**
     * @throws DomainException Jika dokumen sudah disetujui, atau MIME/ukuran tidak sesuai snapshot.
     */
    public function submit(RegistrationDocument $document, UploadedFile $file, User $uploader): DocumentSubmission
    {
        if ($document->current_status === DocumentStatus::Approved) {
            throw new DomainException('Dokumen yang sudah disetujui tidak dapat diunggah ulang.');
        }

        $requirement = $document->requirement;
        $detectedMime = $file->getMimeType();

        if (! in_array($detectedMime, $requirement->allowed_mime_types_snapshot, true)) {
            throw new DomainException('Jenis file tidak diizinkan untuk dokumen ini.');
        }

        $extension = self::EXTENSION_BY_MIME[$detectedMime] ?? null;

        if ($extension === null) {
            throw new DomainException('Jenis file tidak dikenali.');
        }

        $sizeKb = (int) ceil($file->getSize() / 1024);

        if ($sizeKb > $requirement->max_file_size_kb_snapshot) {
            throw new DomainException("Ukuran file maksimal {$requirement->max_file_size_kb_snapshot} KB.");
        }

        return DB::transaction(function () use ($document, $file, $uploader, $requirement, $detectedMime, $extension) {
            $version = ((int) $document->submissions()->max('version_number')) + 1;
            $directory = 'registrations/'.$document->registration_id.'/documents/'.Str::slug($requirement->requirement_code);
            $filename = "v{$version}-".Str::uuid()->toString().'.'.$extension;

            $path = Storage::disk('local')->putFileAs($directory, $file, $filename);

            $submission = DocumentSubmission::query()->create([
                'registration_document_id' => $document->id,
                'version_number' => $version,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $detectedMime,
                'file_size_bytes' => $file->getSize(),
                'status' => 'submitted',
                'uploaded_by' => $uploader->id,
                'submitted_at' => now(),
            ]);

            $document->update([
                'current_status' => DocumentStatus::UnderReview,
                'latest_submission_id' => $submission->id,
            ]);

            $registration = $document->registration;

            if ($registration->status === RegistrationStatus::RevisionRequired) {
                $this->statusService->transitionTo($registration, RegistrationStatus::UnderReview, $uploader);
            }

            return $submission;
        });
    }
}
