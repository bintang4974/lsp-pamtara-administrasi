<x-layouts.admin title="Pengaturan Aplikasi">
    <h1 class="text-xl font-semibold text-gray-900">Pengaturan Aplikasi</h1>
    <p class="mt-1 text-sm text-gray-500">Satu rekening/VA berlaku untuk semua peserta. Perubahan di sini tercatat di audit log.</p>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 max-w-2xl space-y-8">
        @csrf
        @method('PUT')

        <section class="rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="font-semibold text-gray-900">Lembaga</h2>

            <div class="mt-4">
                <x-input-label for="institution_name" value="Nama Lembaga" />
                <x-text-input id="institution_name" name="institution_name" class="block mt-1 w-full"
                    :value="old('institution_name', $settings->get('institution.name')?->value)" required />
                <x-input-error :messages="$errors->get('institution_name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="institution_contact" value="Kontak (opsional)" />
                <x-text-input id="institution_contact" name="institution_contact" class="block mt-1 w-full"
                    :value="old('institution_contact', $settings->get('institution.contact')?->value)" />
                <x-input-error :messages="$errors->get('institution_contact')" class="mt-2" />
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="font-semibold text-gray-900">Rekening/VA Pembayaran Bersama</h2>
            <p class="mt-1 text-xs text-gray-500">Bukan VA unik per peserta. Instruksi hanya tampil ke peserta setelah lolos administrasi.</p>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="payment_bank_name" value="Nama Bank" />
                    <x-text-input id="payment_bank_name" name="payment_bank_name" class="block mt-1 w-full"
                        :value="old('payment_bank_name', $settings->get('payment.bank_name')?->value)" />
                    <x-input-error :messages="$errors->get('payment_bank_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="payment_account_number" value="Nomor Rekening/VA" />
                    <x-text-input id="payment_account_number" name="payment_account_number" class="block mt-1 w-full"
                        :value="old('payment_account_number', $settings->get('payment.account_number')?->value)" />
                    <x-input-error :messages="$errors->get('payment_account_number')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="payment_account_holder" value="Nama Pemegang Rekening" />
                <x-text-input id="payment_account_holder" name="payment_account_holder" class="block mt-1 w-full"
                    :value="old('payment_account_holder', $settings->get('payment.account_holder')?->value)" />
                <x-input-error :messages="$errors->get('payment_account_holder')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="payment_amount" value="Nominal (Rp)" />
                <x-text-input id="payment_amount" name="payment_amount" type="number" step="0.01" min="0" class="block mt-1 w-full"
                    :value="old('payment_amount', $settings->get('payment.amount')?->value)" />
                <p class="mt-1 text-xs text-gray-500">Wajib diisi sebelum sistem bisa membuat tagihan otomatis saat peserta lolos administrasi.</p>
                <x-input-error :messages="$errors->get('payment_amount')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="payment_instructions" value="Instruksi Pembayaran" />
                <textarea id="payment_instructions" name="payment_instructions" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('payment_instructions', $settings->get('payment.instructions')?->value) }}</textarea>
                <x-input-error :messages="$errors->get('payment_instructions')" class="mt-2" />
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="font-semibold text-gray-900">PDF Undangan</h2>
            <div class="mt-4">
                <x-input-label for="pdf_footer" value="Footer PDF (opsional)" />
                <x-text-input id="pdf_footer" name="pdf_footer" class="block mt-1 w-full"
                    :value="old('pdf_footer', $settings->get('pdf.footer')?->value)" />
                <x-input-error :messages="$errors->get('pdf_footer')" class="mt-2" />
            </div>
        </section>

        <button type="submit" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
            Simpan Pengaturan
        </button>
    </form>
</x-layouts.admin>
