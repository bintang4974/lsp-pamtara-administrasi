<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Nomor aman race condition via tabel number_sequences + lockForUpdate dalam transaction
 * (lihat ARCHITECTURE.md §5). Format default: REG-{YYYY}-{000001}, UND-{YYYY}-{000001}.
 */
class NumberGenerator
{
    public function next(string $key, string $prefix, int $padLength = 6): string
    {
        $year = now()->year;
        $sequenceKey = "{$key}:{$year}";
        $value = $this->incrementSequence($sequenceKey);

        return sprintf('%s-%d-%0'.$padLength.'d', $prefix, $year, $value);
    }

    private function incrementSequence(string $sequenceKey, bool $isRetry = false): int
    {
        return DB::transaction(function () use ($sequenceKey, $isRetry) {
            $row = DB::table('number_sequences')->where('key', $sequenceKey)->lockForUpdate()->first();

            if ($row === null) {
                try {
                    DB::table('number_sequences')->insert(['key' => $sequenceKey, 'last_value' => 1]);

                    return 1;
                } catch (QueryException $e) {
                    if ($isRetry) {
                        throw $e;
                    }

                    // Baris dibuat transaksi lain tepat sebelum insert ini (race condition); coba sekali lagi.
                    return $this->incrementSequence($sequenceKey, isRetry: true);
                }
            }

            DB::table('number_sequences')->where('key', $sequenceKey)->increment('last_value');

            return $row->last_value + 1;
        });
    }
}
