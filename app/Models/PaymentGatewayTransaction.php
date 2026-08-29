<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentGatewayTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'provider',
        'order_id',
        'transaction_id',
        'transaction_status',
        'gross_amount',
        'payment_type',
        'signature_key',
        'raw_response',
    ];

    protected $casts = [
        'gross_amount' => 'integer',
        'raw_response' => 'array',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
