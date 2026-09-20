<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'invoice_number',
        'file_path',
        'treasurer_name',
        'treasurer_signature_path',
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

            if (empty($invoice->treasurer_name)) {
                $treasurer = User::getActiveTreasurer();
                $invoice->treasurer_name = $treasurer?->name ?? config('school.treasurer_name', 'Hj. Titi Nurhayati, S. Pd');
                $invoice->treasurer_signature_path = $treasurer?->signature_path;
            }
        });
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function getSignatureBase64(): ?string
    {
        if ($this->treasurer_signature_path && Storage::disk('public')->exists($this->treasurer_signature_path)) {
            $image = Storage::disk('public')->get($this->treasurer_signature_path);
            $mime = Storage::disk('public')->mimeType($this->treasurer_signature_path);

            return 'data:'.$mime.';base64,'.base64_encode($image);
        }

        return null;
    }
}
