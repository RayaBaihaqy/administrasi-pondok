<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'parent_id',
        'payment_type_id',
        'academic_year_id',
        'bill_number',
        'billing_period',
        'billing_date',
        'due_date',
        'amount',
        'paid_amount',
        'outstanding_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'billing_period' => 'date',
        'billing_date' => 'date',
        'due_date' => 'date',
        'amount' => 'integer',
        'paid_amount' => 'integer',
        'outstanding_amount' => 'integer',
    ];

    // ─── Status Constants ─────────────────────────────────────

    const STATUS_UNPAID = 'unpaid';

    const STATUS_PAID = 'paid';

    const STATUS_OVERDUE = 'overdue';

    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_UNPAID => 'Belum Lunas',
        self::STATUS_PAID => 'Lunas',
        self::STATUS_OVERDUE => 'Terlambat',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    // ─── Boot ────────────────────────────────────────────────

    protected static function booted()
    {
        static::creating(function (Bill $bill) {
            if (empty($bill->bill_number)) {
                $nis = $bill->student?->nis ?? null;
                if (! $nis && $bill->student_id) {
                    $nis = Student::find($bill->student_id)?->nis;
                }
                $cleanNis = $nis ? str_replace([' ', '-'], '', $nis) : 'GEN';
                $period = $bill->billing_period ? Carbon::parse($bill->billing_period)->format('Ym') : now()->format('Ym');
                $bill->bill_number = 'INV-'.$cleanNis.'-'.$period.'-'.Str::upper(Str::random(4));
            }
            if (empty($bill->parent_id) && $bill->student_id) {
                $bill->parent_id = Student::find($bill->student_id)?->parent_id;
            }
            if (is_null($bill->paid_amount)) {
                $bill->paid_amount = 0;
            }
            if (is_null($bill->outstanding_amount)) {
                $bill->outstanding_amount = max(0, (int) $bill->amount - (int) $bill->paid_amount);
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parentProfile(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function successfulPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', Payment::STATUS_SUCCESS);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // ─── Status & Amount Recalculation ───────────────────────

    /**
     * Hitung ulang paid_amount, outstanding_amount, dan status bill secara konsisten berdasarkan transaksi pembayaran berhasil.
     */
    public function recalculateStatusAndAmounts(): void
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return;
        }

        $totalPaid = (int) $this->successfulPayments()->sum('amount');
        $this->paid_amount = $totalPaid;
        $this->outstanding_amount = max(0, (int) $this->amount - $totalPaid);

        if ($this->outstanding_amount <= 0) {
            $this->status = self::STATUS_PAID;
        } elseif (Carbon::now()->startOfDay()->greaterThan(Carbon::parse($this->due_date)->startOfDay())) {
            $this->status = self::STATUS_OVERDUE;
        } else {
            $this->status = self::STATUS_UNPAID;
        }

        $this->save();
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isUnpaid(): bool
    {
        return $this->status === self::STATUS_UNPAID;
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_OVERDUE || ($this->outstanding_amount > 0 && Carbon::now()->startOfDay()->greaterThan(Carbon::parse($this->due_date)->startOfDay()));
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeUnpaid($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE);
    }
}
