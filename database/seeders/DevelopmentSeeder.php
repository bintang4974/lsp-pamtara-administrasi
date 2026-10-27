<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data contoh untuk pengembangan/pengujian SAJA. Dipanggil DatabaseSeeder hanya saat
 * app()->environment('local', 'testing') — lihat CLAUDE.md: jangan seed TUK fiktif sebagai data produksi.
 */
class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin.dev@lsp-pamtara.test'],
            [
                'name' => 'Admin Dev (DUMMY)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->forceFill(['role' => UserRole::Admin, 'status' => 'active'])->save();

        $participant = User::query()->firstOrCreate(
            ['email' => 'peserta.dev@lsp-pamtara.test'],
            [
                'name' => 'Peserta Dev (DUMMY)',
                'phone' => '081200000000',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $participant->forceFill(['role' => UserRole::Participant, 'status' => 'active'])->save();

        Tuk::query()->firstOrCreate(
            ['code' => 'TUK-DUMMY-01'],
            [
                'name' => 'TUK DUMMY 01 (bukan data resmi)',
                'address' => '(DUMMY) Jl. Contoh No. 1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'is_active' => true,
            ]
        );
    }
}
