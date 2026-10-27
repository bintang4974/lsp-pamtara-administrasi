<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CertificationTypeRequest;
use App\Models\CertificationType;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CertificationTypeController extends Controller
{
    public function __construct(private AuditLogger $auditLogger) {}

    public function index(): View
    {
        return view('admin.certification-types.index', [
            'certificationTypes' => CertificationType::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.certification-types.create');
    }

    public function store(CertificationTypeRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $type = CertificationType::query()->create($request->validated());

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'certification_type.created',
                entityType: CertificationType::class,
                entityId: $type->id,
                newValues: $type->only(['code', 'name', 'color_code', 'is_active']),
            );
        });

        return redirect()->route('admin.certification-types.index')->with('status', 'Sertifikasi berhasil ditambahkan.');
    }

    public function edit(CertificationType $certificationType): View
    {
        return view('admin.certification-types.edit', ['certificationType' => $certificationType]);
    }

    public function update(CertificationTypeRequest $request, CertificationType $certificationType): RedirectResponse
    {
        DB::transaction(function () use ($request, $certificationType) {
            $old = $certificationType->only(['code', 'name', 'description', 'color_code', 'is_active']);
            $certificationType->update($request->validated());

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'certification_type.updated',
                entityType: CertificationType::class,
                entityId: $certificationType->id,
                oldValues: $old,
                newValues: $certificationType->only(array_keys($old)),
            );
        });

        return redirect()->route('admin.certification-types.index')->with('status', 'Sertifikasi berhasil diperbarui.');
    }

    public function toggle(Request $request, CertificationType $certificationType): RedirectResponse
    {
        DB::transaction(function () use ($request, $certificationType) {
            $old = $certificationType->is_active;
            $certificationType->update(['is_active' => ! $old]);

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'certification_type.toggled',
                entityType: CertificationType::class,
                entityId: $certificationType->id,
                oldValues: ['is_active' => $old],
                newValues: ['is_active' => $certificationType->is_active],
            );
        });

        return redirect()->route('admin.certification-types.index')->with('status', 'Status sertifikasi diperbarui.');
    }
}
