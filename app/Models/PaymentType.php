<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'billing_type',
        'allows_installment',
        'default_amount',
        'default_due_day',
        'is_active',
        'description',
    ];

    protected $casts = [
        'allows_installment' => 'boolean',
        'is_active' => 'boolean',
        'default_amount' => 'integer',
        'default_due_day' => 'integer',
    ];

    // ─── Constants ───────────────────────────────────────────

    const CODE_SPP = 'SPP';
    const CODE_PTS = 'PTS';
    const CODE_PAS = 'PAS';
    const CODE_PAT = 'PAT';
    const CODE_LDKS = 'LDKS';
    const CODE_ST = 'ST';
    const CODE_AT = 'AT';
    const CODE_DAFTAR_ULANG = 'DAFTAR_ULANG';
    const CODE_PENDAFTARAN_BARU = 'PENDAFTARAN_BARU';

    const BILLING_TYPE_MONTHLY = 'monthly';

    const BILLING_TYPE_ONE_TIME = 'one_time';

    const BILLING_TYPE_CUSTOM = 'custom';

    const BILLING_TYPES = [
        self::BILLING_TYPE_MONTHLY => 'Bulanan',
        self::BILLING_TYPE_ONE_TIME => 'Sekali Bayar',
        self::BILLING_TYPE_CUSTOM => 'Insidental',
    ];

    // ─── Relationships ───────────────────────────────────────

    /**
     * Harga per kelas/rombel/tahun ajaran.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(PaymentTypePrice::class);
    }

    // ─── Relationships ───────────────────────────────────────

    /**
     * Override jatuh tempo.
     */
    public function dueDateOverrides(): HasMany
    {
        return $this->hasMany(PaymentDueDateOverride::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMonthly($query)
    {
        return $query->where('billing_type', self::BILLING_TYPE_MONTHLY);
    }
}
