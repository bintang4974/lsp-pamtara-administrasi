<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssessmentScheduleRequest;
use App\Models\AssessmentSchedule;
use App\Models\Assessor;
use App\Models\CertificationType;
use App\Models\Tuk;
use App\Services\Audit\AuditLogger;
use App\Services\Scheduling\ScheduleChangeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentScheduleController extends Controller
{
    public function __construct(
        private AuditLogger $auditLogger,
        private ScheduleChangeService $scheduleChangeService,
    ) {}

    public function index(Request $request): View
    {
        $query = AssessmentSchedule::query()->with(['certificationType', 'tuk'])->withCount('registrations');

        if ($certificationTypeId = $request->input('certification_type_id')) {
            $query->where('certification_type_id', $certificationTypeId);
        }

        if ($tukId = $request->input('tuk_id')) {
            $query->where('tuk_id', $tukId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return view('admin.assessment-schedules.index', [
            'schedules' => $query->orderByDesc('starts_at')->paginate(15)->withQueryString(),
            'certificationTypes' => CertificationType::query()->orderBy('name')->get(),
            'tuks' => Tuk::query()->orderBy('name')->get(),
            'filters' => $request->only(['certification_type_id', 'tuk_id', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.assessment-schedules.create', $this->formOptions());
    }

    public function store(AssessmentScheduleRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->safe()->except('assessor_ids');
            $schedule = AssessmentSchedule::query()->create([...$data, 'created_by' => $request->user()->id]);

            if ($assessorIds = $request->input('assessor_ids')) {
                $schedule->assessors()->sync($assessorIds);
            }

            $this->auditLogger->log(
                actor: $request->user(),
                action: 'schedule.created',
                entityType: AssessmentSchedule::class,
                entityId: $schedule->id,
                newValues: $schedule->only(['schedule_code', 'certification_type_id', 'tuk_id', 'starts_at', 'ends_at', 'status']),
            );
        });

        return redirect()->route('admin.assessment-schedules.index')->with('status', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(AssessmentSchedule $assessmentSchedule): View
    {
        return view('admin.assessment-schedules.edit', [
            ...$this->formOptions(),
            'assessmentSchedule' => $assessmentSchedule->load('assessors'),
        ]);
    }

    public function update(AssessmentScheduleRequest $request, AssessmentSchedule $assessmentSchedule): RedirectResponse
    {
        $data = $request->safe()->except('assessor_ids');

        $this->scheduleChangeService->update(
            schedule: $assessmentSchedule,
            data: $data,
            assessorIds: $request->input('assessor_ids'),
            actor: $request->user(),
        );

        return redirect()->route('admin.assessment-schedules.index')->with('status', 'Jadwal berhasil diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'certificationTypes' => CertificationType::query()->where('is_active', true)->orderBy('name')->get(),
            'tuks' => Tuk::query()->where('is_active', true)->orderBy('name')->get(),
            'assessors' => Assessor::query()->where('is_active', true)->orderBy('name')->get(),
        ];
    }
}
