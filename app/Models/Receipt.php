<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'receipt_number',
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
        static::creating(function (Receipt $receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = 'REC-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            }

            if (empty($receipt->treasurer_name)) {
                $treasurer = User::getActiveTreasurer();
                $receipt->treasurer_name = $treasurer?->name ?? config('school.treasurer_name', 'Hj. Titi Nurhayati, S. Pd');
                $receipt->treasurer_signature_path = $treasurer?->signature_path;
            }
        });
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
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
