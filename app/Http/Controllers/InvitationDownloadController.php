<?php

namespace App\Http\Controllers;

use App\Models\InvitationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvitationDownloadController extends Controller
{
    public function show(Request $request, InvitationDocument $invitation): StreamedResponse
    {
        $this->authorize('view', $invitation);

        abort_unless(Storage::disk(config('pamtara.private_disk'))->exists($invitation->file_path), 404);

        return Storage::disk(config('pamtara.private_disk'))->download(
            $invitation->file_path,
            "{$invitation->document_number}.pdf",
            ['X-Content-Type-Options' => 'nosniff'],
        );
    }
}
