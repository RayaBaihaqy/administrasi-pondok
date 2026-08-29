<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bill_id',
        'student_id',
        'parent_id',
        'payment_number',
        'amount',
        'source',
        'method',
        'status',
        'snap_token',
        'transaction_reference',
        'paid_at',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    // ─── Constants ───────────────────────────────────────────

    const SOURCE_MIDTRANS = 'midtrans';

    const SOURCE_MANUAL = 'manual';

    const STATUS_PENDING = 'pending';

    const STATUS_SUCCESS = 'success';

    const STATUS_FAILED = 'failed';

    const STATUS_EXPIRED = 'expired';

    const STATUS_CANCELLED = 'cancelled';

    const SOURCES = [
        self::SOURCE_MIDTRANS => 'Midtrans Online',
        self::SOURCE_MANUAL => 'Manual / Offline',
    ];

    const STATUSES = [
        self::STATUS_PENDING => 'Menunggu Pembayaran',
        self::STATUS_SUCCESS => 'Berhasil / Lunas',
        self::STATUS_FAILED => 'Gagal',
        self::STATUS_EXPIRED => 'Kadaluwarsa',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    // ─── Boot ────────────────────────────────────────────────

    protected static function booted()
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = self::generatePaymentNumberFromBill(
                    $payment->bill ?? ($payment->bill_id ? Bill::find($payment->bill_id) : null),
                    $payment->source ?? self::SOURCE_MANUAL,
                    $payment->student
                );
            }
        });
    }

    public static function generatePaymentNumberFromBill(?Bill $bill, string $source = self::SOURCE_MANUAL, $student = null): string
    {
        $prefix = ($source === self::SOURCE_MIDTRANS) ? 'PAY-ONLINE-' : 'PAY-MANUAL-';

        if ($bill && $bill->bill_number) {
            $suffix = preg_replace('/^(INV|BILL)-/i', '', $bill->bill_number);
            return $prefix . $suffix;
        }

        $nis = $student?->nis ?? ($bill?->student?->nis ?? null);
        $cleanNis = $nis ? str_replace([' ', '-'], '', $nis) : 'GEN';
        return $prefix . $cleanNis . '-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
    }

    // ─── Relationships ───────────────────────────────────────

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parentProfile(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function gatewayTransactions(): HasMany
    {
        return $this->hasMany(PaymentGatewayTransaction::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(PaymentEvidence::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    // ─── Helper Methods ──────────────────────────────────────

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isManual(): bool
    {
        return $this->source === self::SOURCE_MANUAL;
    }

    public function isMidtrans(): bool
    {
        return $this->source === self::SOURCE_MIDTRANS;
    }
}
