<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'invoice_number',
        'file_path',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $nis = $invoice->bill?->student?->nis ?? null;
                if (! $nis && $invoice->bill_id) {
                    $nis = Bill::find($invoice->bill_id)?->student?->nis;
                }
                $cleanNis = $nis ? str_replace([' ', '-'], '', $nis) : 'GEN';
                $invoice->invoice_number = 'INV-'.$cleanNis.'-'.now()->format('Ym').'-'.Str::upper(Str::random(4));
            }
        });
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
}
