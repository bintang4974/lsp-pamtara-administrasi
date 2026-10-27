<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssessorRequest;
use App\Models\Assessor;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessorController extends Controller
{
    public function __construct(private AuditLogger $auditLogger) {}

    public function index(): View
    {
        return view('admin.assessors.index', [
            'assessors' => Assessor::query()->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.assessors.create');
    }

    public function store(AssessorRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $assessor = Assessor::query()->create($request->validated());

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'assessor.created',
                entityType: Assessor::class,
                entityId: $assessor->id,
                newValues: $assessor->only(['name', 'registration_number', 'is_active']),
            );
        });

        return redirect()->route('admin.assessors.index')->with('status', 'Asesor berhasil ditambahkan.');
    }

    public function edit(Assessor $assessor): View
    {
        return view('admin.assessors.edit', ['assessor' => $assessor]);
    }

    public function update(AssessorRequest $request, Assessor $assessor): RedirectResponse
    {
        DB::transaction(function () use ($request, $assessor) {
            $old = $assessor->only(['registration_number', 'name', 'phone', 'email', 'is_active']);
            $assessor->update($request->validated());

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'assessor.updated',
                entityType: Assessor::class,
                entityId: $assessor->id,
                oldValues: $old,
                newValues: $assessor->only(array_keys($old)),
            );
        });

        return redirect()->route('admin.assessors.index')->with('status', 'Asesor berhasil diperbarui.');
    }

    public function toggle(Request $request, Assessor $assessor): RedirectResponse
    {
        DB::transaction(function () use ($request, $assessor) {
            $old = $assessor->is_active;
            $assessor->update(['is_active' => ! $old]);

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'assessor.toggled',
                entityType: Assessor::class,
                entityId: $assessor->id,
                oldValues: ['is_active' => $old],
                newValues: ['is_active' => $assessor->is_active],
            );
        });

        return redirect()->route('admin.assessors.index')->with('status', 'Status asesor diperbarui.');
    }
}
