<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'registration_id', 'expected_amount', 'confirmed_amount', 'status', 'paid_at',
    'confirmed_by', 'confirmed_at', 'transaction_reference', 'admin_note',
])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'confirmed_amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(PaymentSubmission::class);
    }
}
