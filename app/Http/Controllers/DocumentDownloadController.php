<?php

namespace App\Http\Controllers;

use App\Models\DocumentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Unduh dokumen privat hanya lewat Policy (admin, atau pemilik pendaftaran). Tidak ada
 * storage:link; file disajikan lewat controller ini (lihat ARCHITECTURE.md §6).
 */
class DocumentDownloadController extends Controller
{
    public function show(Request $request, DocumentSubmission $submission): StreamedResponse
    {
        $this->authorize('view', $submission);

        abort_unless(Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download(
            $submission->file_path,
            $submission->original_filename,
            ['X-Content-Type-Options' => 'nosniff'],
        );
    }
}
