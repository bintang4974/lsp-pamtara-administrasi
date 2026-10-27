<x-layouts.participant title="Data Diri">
    <x-stepper :current="4" />

    <h1 class="mt-4 text-xl font-semibold text-gray-900">Lengkapi Data Diri</h1>
    <p class="mt-1 text-sm text-gray-500">Data ini terkunci setelah pendaftaran dikirim; perubahan selanjutnya lewat admin.</p>

    <form method="POST" action="{{ route('participant.registration-wizard.data.store') }}" class="mt-6 max-w-2xl rounded-lg border border-gray-200 bg-white p-6">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $user->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="phone" value="Nomor HP" />
                <x-text-input id="phone" name="phone" class="block mt-1 w-full" :value="old('phone', $user->phone)" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="nik" value="NIK (opsional, 16 digit)" />
            <x-text-input id="nik" name="nik" class="block mt-1 w-full" :value="old('nik', $profile?->nik)" maxlength="16" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="birth_place" value="Tempat Lahir" />
                <x-text-input id="birth_place" name="birth_place" class="block mt-1 w-full" :value="old('birth_place', $profile?->birth_place)" required />
                <x-input-error :messages="$errors->get('birth_place')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="birth_date" value="Tanggal Lahir" />
                <input id="birth_date" name="birth_date" type="date" required
                    value="{{ old('birth_date', $profile?->birth_date?->format('Y-m-d')) }}"
                    class="mt-1 block w-full min-h-[44px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label value="Jenis Kelamin" />
            <div class="mt-1 flex gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="radio" name="gender" value="male" {{ old('gender', $profile?->gender) === 'male' ? 'checked' : '' }} required class="h-5 w-5">
                    Laki-laki
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="radio" name="gender" value="female" {{ old('gender', $profile?->gender) === 'female' ? 'checked' : '' }} required class="h-5 w-5">
                    Perempuan
                </label>
            </div>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="address" value="Alamat" />
            <textarea id="address" name="address" rows="2" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('address', $profile?->address) }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="city" value="Kota/Kabupaten" />
                <x-text-input id="city" name="city" class="block mt-1 w-full" :value="old('city', $profile?->city)" required />
                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="province" value="Provinsi" />
                <x-text-input id="province" name="province" class="block mt-1 w-full" :value="old('province', $profile?->province)" required />
                <x-input-error :messages="$errors->get('province')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="postal_code" value="Kode Pos (opsional)" />
                <x-text-input id="postal_code" name="postal_code" class="block mt-1 w-full" :value="old('postal_code', $profile?->postal_code)" />
                <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                Buat Pendaftaran
            </button>
        </div>
    </form>
</x-layouts.participant>
