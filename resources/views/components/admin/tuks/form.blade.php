@props(['tuk' => null])

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="code" value="Kode" />
        <x-text-input id="code" name="code" class="block mt-1 w-full" :value="old('code', $tuk?->code)" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="name" value="Nama TUK" />
        <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $tuk?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="address" value="Alamat" />
    <textarea id="address" name="address" rows="2" required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('address', $tuk?->address) }}</textarea>
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="city" value="Kota/Kabupaten" />
        <x-text-input id="city" name="city" class="block mt-1 w-full" :value="old('city', $tuk?->city)" required />
        <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="province" value="Provinsi" />
        <x-text-input id="province" name="province" class="block mt-1 w-full" :value="old('province', $tuk?->province)" required />
        <x-input-error :messages="$errors->get('province')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="phone" value="Telepon (opsional)" />
        <x-text-input id="phone" name="phone" class="block mt-1 w-full" :value="old('phone', $tuk?->phone)" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="email" value="Email (opsional)" />
        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $tuk?->email)" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>
</div>

<div class="mt-4 flex items-center gap-2">
    <input id="is_active" name="is_active" type="checkbox" value="1"
        {{ old('is_active', $tuk?->is_active ?? true) ? 'checked' : '' }}
        class="h-5 w-5 rounded border-gray-300 text-blue-700">
    <x-input-label for="is_active" value="Aktif (ditawarkan untuk jadwal baru)" />
</div>
