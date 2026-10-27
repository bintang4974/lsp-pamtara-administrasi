@props(['documentRequirement' => null])

@php
    $selectedMimes = old('allowed_mime_types', $documentRequirement?->allowed_mime_types ?? ['application/pdf', 'image/jpeg', 'image/png']);
    $mimeOptions = [
        'application/pdf' => 'PDF',
        'image/jpeg' => 'JPG/JPEG',
        'image/png' => 'PNG',
    ];
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="code" value="Kode" />
        <x-text-input id="code" name="code" class="block mt-1 w-full" :value="old('code', $documentRequirement?->code)" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="name" value="Nama Dokumen" />
        <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $documentRequirement?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="description" value="Deskripsi (opsional)" />
    <textarea id="description" name="description" rows="2"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('description', $documentRequirement?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label value="Jenis File Diizinkan" />
    <div class="mt-1 flex flex-wrap gap-4">
        @foreach($mimeOptions as $value => $label)
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="allowed_mime_types[]" value="{{ $value }}"
                    {{ in_array($value, $selectedMimes, true) ? 'checked' : '' }}
                    class="h-5 w-5 rounded border-gray-300 text-blue-700">
                {{ $label }}
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('allowed_mime_types')" class="mt-2" />
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="max_file_size_kb" value="Ukuran Maksimal (KB)" />
        <x-text-input id="max_file_size_kb" name="max_file_size_kb" type="number" min="1" class="block mt-1 w-full"
            :value="old('max_file_size_kb', $documentRequirement?->max_file_size_kb ?? 2048)" required />
        <x-input-error :messages="$errors->get('max_file_size_kb')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="sort_order" value="Urutan Tampil" />
        <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="block mt-1 w-full"
            :value="old('sort_order', $documentRequirement?->sort_order ?? 0)" required />
        <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
    </div>
</div>

<div class="mt-4 flex flex-col gap-2">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input id="is_required" name="is_required" type="checkbox" value="1"
            {{ old('is_required', $documentRequirement?->is_required ?? true) ? 'checked' : '' }}
            class="h-5 w-5 rounded border-gray-300 text-blue-700">
        Wajib diunggah (menentukan kelulusan administrasi)
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input id="is_active" name="is_active" type="checkbox" value="1"
            {{ old('is_active', $documentRequirement?->is_active ?? true) ? 'checked' : '' }}
            class="h-5 w-5 rounded border-gray-300 text-blue-700">
        Aktif (berlaku untuk pendaftaran baru)
    </label>
</div>
