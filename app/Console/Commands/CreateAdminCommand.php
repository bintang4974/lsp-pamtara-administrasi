<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Buat akun admin pertama secara interaktif (aman, tidak lewat registrasi publik)';

    public function handle(): int
    {
        $this->info('Membuat akun admin LSP Pamtara.');

        $name = $this->ask('Nama lengkap admin');
        $email = $this->ask('Email admin');
        $password = $this->secret('Password (minimal 8 karakter)');
        $passwordConfirmation = $this->secret('Ulangi password');

        $validator = Validator::make(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if (! $this->confirm("Buat admin dengan email {$email}?")) {
            $this->warn('Dibatalkan.');

            return self::SUCCESS;
        }

        $admin = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $admin->forceFill([
            'role' => UserRole::Admin,
            'status' => 'active',
            'email_verified_at' => now(),
        ])->save();

        $this->info("Admin '{$admin->name}' <{$admin->email}> berhasil dibuat.");

        return self::SUCCESS;
    }
}
