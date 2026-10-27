@props([
    'assessmentSchedule' => null,
    'certificationTypes',
    'tuks',
    'assessors',
])

@php
    $selectedAssessorIds = old('assessor_ids', $assessmentSchedule?->assessors->pluck('id')->all() ?? []);
    $fmt = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="schedule_code" value="Kode Jadwal" />
        <x-text-input id="schedule_code" name="schedule_code" class="block mt-1 w-full"
            :value="old('schedule_code', $assessmentSchedule?->schedule_code)" required />
        <x-input-error :messages="$errors->get('schedule_code')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" required
            class="mt-1 block w-full min-h-[44px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            @foreach(['draft' => 'Draft', 'open' => 'Dibuka', 'closed' => 'Ditutup', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" {{ old('status', $assessmentSchedule?->status?->value ?? 'draft') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="certification_type_id" value="Sertifikasi" />
        <select id="certification_type_id" name="certification_type_id" required
            class="mt-1 block w-full min-h-[44px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            <option value="">Pilih sertifikasi</option>
            @foreach($certificationTypes as $type)
                <option value="{{ $type->id }}" {{ (int) old('certification_type_id', $assessmentSchedule?->certification_type_id) === $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('certification_type_id')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="tuk_id" value="TUK" />
        <select id="tuk_id" name="tuk_id" required
            class="mt-1 block w-full min-h-[44px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            <option value="">Pilih TUK</option>
            @foreach($tuks as $tuk)
                <option value="{{ $tuk->id }}" {{ (int) old('tuk_id', $assessmentSchedule?->tuk_id) === $tuk->id ? 'selected' : '' }}>
                    {{ $tuk->name }} ({{ $tuk->city }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('tuk_id')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-3">
    <div>
        <x-input-label for="starts_at" value="Mulai" />
        <input id="starts_at" name="starts_at" type="datetime-local" required
            value="{{ old('starts_at', $fmt($assessmentSchedule?->starts_at)) }}"
            class="mt-1 block w-full min-h-[44px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="ends_at" value="Selesai" />
        <input id="ends_at" name="ends_at" type="datetime-local" required
            value="{{ old('ends_at', $fmt($assessmentSchedule?->ends_at)) }}"
            class="mt-1 block w-full min-h-[44px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        <x-input-error :messages="$errors->get('ends_at')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="registration_deadline" value="Batas Pendaftaran" />
        <input id="registration_deadline" name="registration_deadline" type="datetime-local" required
            value="{{ old('registration_deadline', $fmt($assessmentSchedule?->registration_deadline)) }}"
            class="mt-1 block w-full min-h-[44px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        <x-input-error :messages="$errors->get('registration_deadline')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label value="Asesor Bertugas" />
    <p class="text-xs text-gray-500">Tidak ada batas jumlah asesor per jadwal.</p>
    <div class="mt-2 grid gap-2 sm:grid-cols-2">
        @forelse($assessors as $assessor)
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="assessor_ids[]" value="{{ $assessor->id }}"
                    {{ in_array($assessor->id, $selectedAssessorIds, true) ? 'checked' : '' }}
                    class="h-5 w-5 rounded border-gray-300 text-blue-700">
                {{ $assessor->name }}
            </label>
        @empty
            <p class="text-sm text-gray-500">Belum ada asesor aktif. Tambahkan asesor terlebih dahulu.</p>
        @endforelse
    </div>
    <x-input-error :messages="$errors->get('assessor_ids')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="notes" value="Catatan (opsional)" />
    <textarea id="notes" name="notes" rows="2"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('notes', $assessmentSchedule?->notes) }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
