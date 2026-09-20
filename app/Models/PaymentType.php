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

    const CODE_PENDAFTARAN_BARU = 'PENDAFTARAN_BARU';

    const CODE_DAFTAR_ULANG_GANJIL = 'DAFTAR_ULANG_GANJIL';

    const CODE_ASTS_PTS_GANJIL = 'ASTS_PTS_GANJIL';

    const CODE_ASAS_GANJIL = 'ASAS_GANJIL';

    const CODE_LDKS = 'LDKS';

    const CODE_DAFTAR_ULANG_GENAP = 'DAFTAR_ULANG_GENAP';

    const CODE_ASTS_PTS_GENAP = 'ASTS_PTS_GENAP';

    const CODE_ASATA_PAT = 'ASATA_PAT';

    const CODE_STUDY_TOUR = 'STUDY_TOUR';

    const CODE_AKHIR_TAHUN = 'AKHIR_TAHUN';

    // Backward compatibility aliases
    const CODE_PTS = 'ASTS_PTS_GANJIL';

    const CODE_PAS = 'ASAS_GANJIL';

    const CODE_PAT = 'ASATA_PAT';

    const CODE_ST = 'STUDY_TOUR';

    const CODE_AT = 'AKHIR_TAHUN';

    const CODE_DAFTAR_ULANG = 'DAFTAR_ULANG_GANJIL';

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
