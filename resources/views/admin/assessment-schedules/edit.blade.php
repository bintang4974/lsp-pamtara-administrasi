<x-layouts.admin title="Ubah Jadwal">
    <div class="mx-auto max-w-3xl">
        <h1 class="text-xl font-semibold text-gray-900">Ubah Jadwal Ujikom</h1>

        @if($assessmentSchedule->registrations()->exists())
            <x-alert type="warning" class="mt-4">
                Jadwal ini sudah memiliki peserta terdaftar. Perubahan waktu/TUK akan menandai undangan yang sudah terbit sebagai usang dan mengirim notifikasi ke peserta.
            </x-alert>
        @endif

        <form method="POST" action="{{ route('admin.assessment-schedules.update', $assessmentSchedule) }}" class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
            @csrf
            @method('PUT')
            <x-admin.assessment-schedules.form
                :assessment-schedule="$assessmentSchedule"
                :certification-types="$certificationTypes"
                :tuks="$tuks"
                :assessors="$assessors" />

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.assessment-schedules.index') }}" class="inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
