<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTypePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_type_id',
        'academic_year_id',
        'class_level',
        'rombel',
        'amount',
    ];

    protected $casts = [
        'class_level' => 'integer',
        'amount' => 'integer',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getClassRombelLabelAttribute(): string
    {
        if (! $this->class_level) {
            return 'Semua Kelas';
        }

        return 'Kelas '.$this->class_level.($this->rombel ? '-'.$this->rombel : ' (Semua Rombel)');
    }
}
