@props(['assessor' => null])

<div>
    <x-input-label for="name" value="Nama Asesor" />
    <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $assessor?->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="registration_number" value="Nomor Registrasi (opsional)" />
    <x-text-input id="registration_number" name="registration_number" class="block mt-1 w-full" :value="old('registration_number', $assessor?->registration_number)" />
    <x-input-error :messages="$errors->get('registration_number')" class="mt-2" />
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="phone" value="Telepon (opsional)" />
        <x-text-input id="phone" name="phone" class="block mt-1 w-full" :value="old('phone', $assessor?->phone)" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="email" value="Email (opsional)" />
        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $assessor?->email)" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>
</div>

<div class="mt-4 flex items-center gap-2">
    <input id="is_active" name="is_active" type="checkbox" value="1"
        {{ old('is_active', $assessor?->is_active ?? true) ? 'checked' : '' }}
        class="h-5 w-5 rounded border-gray-300 text-blue-700">
    <x-input-label for="is_active" value="Aktif (dapat ditugaskan ke jadwal baru)" />
</div>
