<x-layouts.admin title="Laporan Pembayaran">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Laporan Pembayaran</h1>
        <a href="{{ route('admin.reports.payments.export', $filters) }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Ekspor Excel
        </a>
    </div>

    <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
        Rekap penerimaan (terkonfirmasi): <strong>Rp {{ number_format($confirmedTotal, 0, ',', '.') }}</strong>
    </div>

    <form method="GET" class="mt-4 grid gap-3 sm:grid-cols-4">
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <select name="status" class="min-h-[44px] rounded-md border-gray-300 text-sm">
            <option value="">Semua Status</option>
            @foreach(['unpaid','awaiting_payment','under_review','revision_required','paid','cancelled'] as $s)
                <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ \App\Enums\PaymentStatus::from($s)->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Filter</button>
    </form>

    @if($payments->isEmpty())
        <div class="mt-6"><x-empty-state title="Tidak ada data" /></div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">No. Pendaftaran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Diharapkan</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Diterima</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payments as $payment)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $payment->registration->registration_number }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $payment->registration->user->name }}</td>
                            <td class="px-4 py-3 text-gray-700">Rp {{ number_format($payment->expected_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $payment->confirmed_amount ? 'Rp '.number_format($payment->confirmed_amount, 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-3"><x-status-badge :label="$payment->status->label()" tone="info" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    @endif
</x-layouts.admin>
