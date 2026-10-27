<?php

namespace App\Services\Imports;

use App\Enums\UserRole;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use App\Models\ParticipantProfile;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Dua tahap: (1) unggah + validasi -> preview tersimpan di import_batches/import_rows,
 * (2) konfirmasi -> commit dalam transaction. Import HANYA membuat akun peserta (tidak
 * menyentuh status dokumen/pembayaran). Password acak + tautan aktivasi, tidak pernah
 * plaintext (lihat PRD.md §5.8, DATABASE.md §6).
 */
class ParticipantImportService
{
    /** @var array<int, string> */
    public const REQUIRED_HEADERS = [
        'name', 'email', 'phone', 'nik', 'birth_place', 'birth_date',
        'gender', 'address', 'city', 'province', 'postal_code',
    ];

    public function __construct(private AuditLogger $auditLogger) {}

    /**
     * @throws DomainException Jika header kolom tidak sesuai template.
     */
    public function preview(UploadedFile $file, User $admin): ImportBatch
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        if (empty($rows)) {
            throw new DomainException('File kosong atau tidak bisa dibaca.');
        }

        $header = array_map(fn ($h) => Str::of((string) $h)->trim()->lower()->value(), $rows[0]);
        $missing = array_diff(self::REQUIRED_HEADERS, $header);

        if (! empty($missing)) {
            throw new DomainException('Header kolom tidak sesuai template. Kolom hilang: '.implode(', ', $missing));
        }

        $columnIndex = array_flip($header);
        $dataRows = array_slice($rows, 1);

        return DB::transaction(function () use ($dataRows, $columnIndex, $file, $admin) {
            $batch = ImportBatch::query()->create([
                'admin_user_id' => $admin->id,
                'original_filename' => $file->getClientOriginalName(),
                'status' => 'previewed',
            ]);

            $seenEmails = [];
            $validCount = 0;

            foreach ($dataRows as $i => $row) {
                if (empty(array_filter($row, fn ($v) => trim((string) $v) !== ''))) {
                    continue;
                }

                $rowNumber = $i + 2;
                $data = $this->extractRow($row, $columnIndex);
                $errors = $this->validateRow($data, $seenEmails);

                if (empty($errors) && ! empty($data['email'])) {
                    $seenEmails[] = Str::lower($data['email']);
                }

                $isValid = empty($errors);
                $validCount += $isValid ? 1 : 0;

                ImportRow::query()->create([
                    'import_batch_id' => $batch->id,
                    'row_number' => $rowNumber,
                    'raw_data' => $data,
                    'is_valid' => $isValid,
                    'errors' => $errors ?: null,
                ]);
            }

            $total = $batch->rows()->count();

            $batch->update([
                'summary' => [
                    'total_rows' => $total,
                    'valid_rows' => $validCount,
                    'invalid_rows' => $total - $validCount,
                ],
            ]);

            $this->auditLogger->log(
                actor: $admin,
                action: 'participant_import.previewed',
                entityType: ImportBatch::class,
                entityId: $batch->id,
                newValues: ['total_rows' => $total, 'valid_rows' => $validCount],
            );

            return $batch;
        });
    }

    /**
     * @return array<string, int>
     *
     * @throws DomainException Jika batch sudah dikomit/dibuang.
     */
    public function commit(ImportBatch $batch, User $admin): array
    {
        if ($batch->status !== 'previewed') {
            throw new DomainException('Batch ini sudah diproses sebelumnya.');
        }

        return DB::transaction(function () use ($batch, $admin) {
            $created = 0;

            foreach ($batch->rows()->where('is_valid', true)->where('committed', false)->get() as $row) {
                $data = $row->raw_data;

                $user = User::query()->create([
                    'name' => $data['name'],
                    'email' => Str::lower($data['email']),
                    'phone' => $data['phone'] ?? null,
                    'password' => Hash::make(Str::random(32)),
                ]);

                $user->forceFill(['role' => UserRole::Participant, 'status' => 'active'])->save();

                ParticipantProfile::query()->create([
                    'user_id' => $user->id,
                    'nik' => $data['nik'] ?? null,
                    'birth_place' => $data['birth_place'] ?? null,
                    'birth_date' => $data['birth_date'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'address' => $data['address'] ?? null,
                    'city' => $data['city'] ?? null,
                    'province' => $data['province'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                ]);

                // Tautan aktivasi/reset password (broker Laravel). Pengiriman email sebenarnya
                // bergantung konfigurasi MAIL_MAILER; tidak diklaim terkirim di sini.
                Password::sendResetLink(['email' => $user->email]);

                $row->update(['committed' => true]);
                $created++;
            }

            $batch->update([
                'status' => 'committed',
                'committed_at' => now(),
                'summary' => array_merge($batch->summary ?? [], ['created_accounts' => $created]),
            ]);

            $this->auditLogger->log(
                actor: $admin,
                action: 'participant_import.committed',
                entityType: ImportBatch::class,
                entityId: $batch->id,
                newValues: ['created_accounts' => $created],
            );

            return ['created_accounts' => $created];
        });
    }

    public function discard(ImportBatch $batch, User $admin): void
    {
        $batch->update(['status' => 'discarded']);

        $this->auditLogger->log(
            actor: $admin,
            action: 'participant_import.discarded',
            entityType: ImportBatch::class,
            entityId: $batch->id,
        );
    }

    /**
     * @param  array<int, mixed>  $row
     * @param  array<string, int>  $columnIndex
     * @return array<string, mixed>
     */
    private function extractRow(array $row, array $columnIndex): array
    {
        $data = [];

        foreach (self::REQUIRED_HEADERS as $column) {
            $value = $row[$columnIndex[$column]] ?? null;
            $data[$column] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $seenEmails
     * @return array<int, string>
     */
    private function validateRow(array $data, array $seenEmails): array
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'nik' => ['nullable', 'digits:16'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ]);

        $errors = [];

        foreach ($validator->errors()->all() as $message) {
            $errors[] = $message;
        }

        if (! empty($data['email'])) {
            $email = Str::lower($data['email']);

            if (User::query()->where('email', $email)->exists()) {
                $errors[] = 'Email sudah terdaftar di sistem.';
            } elseif (in_array($email, $seenEmails, true)) {
                $errors[] = 'Email duplikat di dalam file.';
            }
        }

        return $errors;
    }
}
