<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ParticipantImportTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ParticipantImportRequest;
use App\Models\ImportBatch;
use App\Services\Imports\ParticipantImportService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ParticipantImportController extends Controller
{
    public function __construct(private ParticipantImportService $imports) {}

    public function create(): View
    {
        return view('admin.imports.create', [
            'batches' => ImportBatch::query()->with('admin')->latest()->take(10)->get(),
        ]);
    }

    public function template(): BinaryFileResponse
    {
        return Excel::download(new ParticipantImportTemplateExport, 'template-import-peserta.xlsx');
    }

    public function store(ParticipantImportRequest $request): RedirectResponse
    {
        try {
            $batch = $this->imports->preview($request->file('file'), $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.imports.show', $batch);
    }

    public function show(ImportBatch $batch): View
    {
        $batch->load('rows');

        return view('admin.imports.show', ['batch' => $batch]);
    }

    public function commit(ImportBatch $batch): RedirectResponse
    {
        try {
            $result = $this->imports->commit($batch, request()->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.imports.show', $batch)
            ->with('status', "{$result['created_accounts']} akun peserta berhasil dibuat. Tautan aktivasi diproses lewat sistem reset password.");
    }

    public function discard(ImportBatch $batch): RedirectResponse
    {
        $this->imports->discard($batch, request()->user());

        return redirect()->route('admin.imports.create')->with('status', 'Batch import dibuang.');
    }
}
