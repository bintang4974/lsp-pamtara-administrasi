<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DocumentCompletenessExport;
use App\Exports\PaymentsExport;
use App\Exports\RegistrationsExport;
use App\Exports\ResultHistoryExport;
use App\Exports\ResultsExport;
use App\Http\Controllers\Controller;
use App\Models\CertificationType;
use App\Models\Tuk;
use App\Services\Reports\DocumentCompletenessReportQuery;
use App\Services\Reports\PaymentReportQuery;
use App\Services\Reports\RegistrationReportQuery;
use App\Services\Reports\ResultHistoryReportQuery;
use App\Services\Reports\ResultReportQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Satu class query per laporan untuk tampilan (paginate) dan ekspor (lihat ARCHITECTURE.md §9).
 * Filter yang sama dipakai di kedua jalur agar hasil selalu konsisten.
 */
class ReportController extends Controller
{
    public function registrations(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to', 'certification_type_id', 'tuk_id', 'status']);

        return view('admin.reports.registrations', [
            'registrations' => (new RegistrationReportQuery)->query($filters)->paginate(20)->withQueryString(),
            'certificationTypes' => CertificationType::query()->orderBy('name')->get(),
            'tuks' => Tuk::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function registrationsExport(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['date_from', 'date_to', 'certification_type_id', 'tuk_id', 'status']);

        return Excel::download(new RegistrationsExport($filters), 'laporan-pendaftaran.xlsx');
    }

    public function documentCompleteness(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to', 'certification_type_id', 'tuk_id']);

        return view('admin.reports.document-completeness', [
            'registrations' => (new DocumentCompletenessReportQuery)->query($filters)->paginate(20)->withQueryString(),
            'certificationTypes' => CertificationType::query()->orderBy('name')->get(),
            'tuks' => Tuk::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function documentCompletenessExport(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['date_from', 'date_to', 'certification_type_id', 'tuk_id']);

        return Excel::download(new DocumentCompletenessExport($filters), 'laporan-kelengkapan-dokumen.xlsx');
    }

    public function payments(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to', 'status', 'certification_type_id']);
        $query = new PaymentReportQuery;

        return view('admin.reports.payments', [
            'payments' => $query->query($filters)->paginate(20)->withQueryString(),
            'confirmedTotal' => $query->confirmedTotal($filters),
            'certificationTypes' => CertificationType::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function paymentsExport(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['date_from', 'date_to', 'status', 'certification_type_id']);

        return Excel::download(new PaymentsExport($filters), 'laporan-pembayaran.xlsx');
    }

    public function results(Request $request): View
    {
        $filters = $request->only(['result', 'announced', 'certification_type_id']);

        return view('admin.reports.results', [
            'results' => (new ResultReportQuery)->query($filters)->paginate(20)->withQueryString(),
            'certificationTypes' => CertificationType::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function resultsExport(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['result', 'announced', 'certification_type_id']);

        return Excel::download(new ResultsExport($filters), 'laporan-hasil.xlsx');
    }

    public function resultHistory(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to']);

        return view('admin.reports.result-history', [
            'histories' => (new ResultHistoryReportQuery)->query($filters)->paginate(20)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function resultHistoryExport(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['date_from', 'date_to']);

        return Excel::download(new ResultHistoryExport($filters), 'laporan-riwayat-hasil.xlsx');
    }
}
