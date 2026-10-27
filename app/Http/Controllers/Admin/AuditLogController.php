<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()->with('actor')->latest();

        if ($action = $request->string('action')->trim()->value()) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($entityType = $request->input('entity_type')) {
            $query->where('entity_type', $entityType);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return view('admin.audit-logs.index', [
            'logs' => $query->paginate(30)->withQueryString(),
            'entityTypes' => AuditLog::query()->select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type'),
            'filters' => $request->only(['action', 'entity_type', 'date_from', 'date_to']),
        ]);
    }
}
