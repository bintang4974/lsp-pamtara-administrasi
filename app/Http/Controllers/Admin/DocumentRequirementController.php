<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentRequirementRequest;
use App\Models\DocumentRequirement;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocumentRequirementController extends Controller
{
    public function __construct(private AuditLogger $auditLogger) {}

    public function index(): View
    {
        return view('admin.document-requirements.index', [
            'documentRequirements' => DocumentRequirement::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.document-requirements.create');
    }

    public function store(DocumentRequirementRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $requirement = DocumentRequirement::query()->create($request->validated());

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'document_requirement.created',
                entityType: DocumentRequirement::class,
                entityId: $requirement->id,
                newValues: $requirement->only(['code', 'name', 'is_required', 'allowed_mime_types', 'max_file_size_kb', 'sort_order', 'is_active']),
            );
        });

        return redirect()->route('admin.document-requirements.index')->with('status', 'Persyaratan dokumen berhasil ditambahkan.');
    }

    public function edit(DocumentRequirement $documentRequirement): View
    {
        return view('admin.document-requirements.edit', ['documentRequirement' => $documentRequirement]);
    }

    public function update(DocumentRequirementRequest $request, DocumentRequirement $documentRequirement): RedirectResponse
    {
        DB::transaction(function () use ($request, $documentRequirement) {
            $old = $documentRequirement->only(['code', 'name', 'description', 'is_required', 'allowed_mime_types', 'max_file_size_kb', 'sort_order', 'is_active']);
            $documentRequirement->update($request->validated());

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'document_requirement.updated',
                entityType: DocumentRequirement::class,
                entityId: $documentRequirement->id,
                oldValues: $old,
                newValues: $documentRequirement->only(array_keys($old)),
            );
        });

        return redirect()->route('admin.document-requirements.index')->with('status', 'Persyaratan dokumen berhasil diperbarui. Perubahan ini tidak mengubah persyaratan pendaftaran yang sudah ada (snapshot).');
    }

    public function toggle(Request $request, DocumentRequirement $documentRequirement): RedirectResponse
    {
        DB::transaction(function () use ($request, $documentRequirement) {
            $old = $documentRequirement->is_active;
            $documentRequirement->update(['is_active' => ! $old]);

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'document_requirement.toggled',
                entityType: DocumentRequirement::class,
                entityId: $documentRequirement->id,
                oldValues: ['is_active' => $old],
                newValues: ['is_active' => $documentRequirement->is_active],
            );
        });

        return redirect()->route('admin.document-requirements.index')->with('status', 'Status persyaratan diperbarui.');
    }

    public function destroy(Request $request, DocumentRequirement $documentRequirement): RedirectResponse
    {
        DB::transaction(function () use ($request, $documentRequirement) {
            $this->auditLogger->log(
                actor: $request->user(),
                action: 'document_requirement.deleted',
                entityType: DocumentRequirement::class,
                entityId: $documentRequirement->id,
                oldValues: $documentRequirement->only(['code', 'name']),
            );

            $documentRequirement->delete();
        });

        return redirect()->route('admin.document-requirements.index')->with('status', 'Persyaratan dokumen berhasil dihapus. Pendaftaran lama tidak terpengaruh karena memakai snapshot.');
    }
}
