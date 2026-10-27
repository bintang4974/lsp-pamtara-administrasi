@props(['certificationType' => null])

<div>
    <x-input-label for="code" value="Kode" />
    <x-text-input id="code" name="code" class="block mt-1 w-full" :value="old('code', $certificationType?->code)" required />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="name" value="Nama" />
    <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $certificationType?->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Deskripsi (opsional)" />
    <textarea id="description" name="description" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('description', $certificationType?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4" x-data="{ color: '{{ old('color_code', $certificationType?->color_code ?? '#2563eb') }}' }">
    <x-input-label for="color_code" value="Warna (hex)" />
    <div class="mt-1 flex items-center gap-3">
        <input type="color" class="h-11 w-16 rounded border-gray-300" x-model="color">
        <input id="color_code" name="color_code" type="text" x-model="color" required
            class="block w-40 rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <p class="mt-1 text-xs text-gray-500">Contoh: #2563eb. Pastikan kontras teks tetap terbaca (lihat pratinjau di halaman info publik).</p>
    <x-input-error :messages="$errors->get('color_code')" class="mt-2" />
</div>

<div class="mt-4 flex items-center gap-2">
    <input id="is_active" name="is_active" type="checkbox" value="1"
        {{ old('is_active', $certificationType?->is_active ?? true) ? 'checked' : '' }}
        class="h-5 w-5 rounded border-gray-300 text-blue-700">
    <x-input-label for="is_active" value="Aktif (ditawarkan untuk pendaftaran baru)" />
</div>
