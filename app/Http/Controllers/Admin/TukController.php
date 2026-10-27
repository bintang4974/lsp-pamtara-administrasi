<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TukRequest;
use App\Models\Tuk;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TukController extends Controller
{
    public function __construct(private AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $query = Tuk::query()->orderBy('name');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        return view('admin.tuks.index', [
            'tuks' => $query->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.tuks.create');
    }

    public function store(TukRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $tuk = Tuk::query()->create($request->validated());

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'tuk.created',
                entityType: Tuk::class,
                entityId: $tuk->id,
                newValues: $tuk->only(['code', 'name', 'city', 'province', 'is_active']),
            );
        });

        return redirect()->route('admin.tuks.index')->with('status', 'TUK berhasil ditambahkan.');
    }

    public function edit(Tuk $tuk): View
    {
        return view('admin.tuks.edit', ['tuk' => $tuk]);
    }

    public function update(TukRequest $request, Tuk $tuk): RedirectResponse
    {
        DB::transaction(function () use ($request, $tuk) {
            $old = $tuk->only(['code', 'name', 'address', 'city', 'province', 'phone', 'email', 'is_active']);
            $tuk->update($request->validated());

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'tuk.updated',
                entityType: Tuk::class,
                entityId: $tuk->id,
                oldValues: $old,
                newValues: $tuk->only(array_keys($old)),
            );
        });

        return redirect()->route('admin.tuks.index')->with('status', 'TUK berhasil diperbarui.');
    }

    public function toggle(Request $request, Tuk $tuk): RedirectResponse
    {
        DB::transaction(function () use ($request, $tuk) {
            $old = $tuk->is_active;
            $tuk->update(['is_active' => ! $old]);

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'tuk.toggled',
                entityType: Tuk::class,
                entityId: $tuk->id,
                oldValues: ['is_active' => $old],
                newValues: ['is_active' => $tuk->is_active],
            );
        });

        return redirect()->route('admin.tuks.index')->with('status', 'Status TUK diperbarui.');
    }
}
