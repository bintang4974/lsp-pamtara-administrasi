<?php

namespace App\Http\Controllers;

use App\Models\PaymentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentProofDownloadController extends Controller
{
    public function show(Request $request, PaymentSubmission $submission): StreamedResponse
    {
        $this->authorize('view', $submission);

        abort_unless(Storage::disk(config('pamtara.private_disk'))->exists($submission->proof_path), 404);

        return Storage::disk(config('pamtara.private_disk'))->download(
            $submission->proof_path,
            $submission->original_filename,
            ['X-Content-Type-Options' => 'nosniff'],
        );
    }
}
