<x-layouts.admin title="Pendaftaran">
    <h1 class="text-xl font-semibold text-gray-900">Pendaftaran</h1>

    <form method="GET" class="mt-4 grid gap-3 sm:grid-cols-5">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nomor/nama..."
            class="min-h-[44px] rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:col-span-2">

        <select name="certification_type_id" class="min-h-[44px] rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Sertifikasi</option>
            @foreach($certificationTypes as $type)
                <option value="{{ $type->id }}" {{ (string) ($filters['certification_type_id'] ?? '') === (string) $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
            @endforeach
        </select>

        <select name="tuk_id" class="min-h-[44px] rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua TUK</option>
            @foreach($tuks as $tuk)
                <option value="{{ $tuk->id }}" {{ (string) ($filters['tuk_id'] ?? '') === (string) $tuk->id ? 'selected' : '' }}>{{ $tuk->name }}</option>
            @endforeach
        </select>

        <select name="status" class="min-h-[44px] rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Status</option>
            @foreach(\App\Enums\RegistrationStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
            @endforeach
        </select>

        <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:col-span-5 sm:w-fit">
            Terapkan Filter
        </button>
    </form>

    @if($registrations->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada pendaftaran" />
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">No. Pendaftaran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Sertifikasi</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">TUK</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($registrations as $registration)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $registration->registration_number }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $registration->user->name }}</td>
                            <td class="px-4 py-3"><x-cert-badge :certification-type="$registration->certificationType" /></td>
                            <td class="px-4 py-3 text-gray-700">{{ $registration->assessmentSchedule->tuk->name }}</td>
                            <td class="px-4 py-3"><x-status-badge :label="$registration->status->label()" tone="info" /></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.registrations.show', $registration) }}" class="min-h-[44px] content-center rounded-md px-3 text-sm font-medium text-blue-700 hover:bg-blue-50">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $registrations->links() }}</div>
    @endif
</x-layouts.admin>
