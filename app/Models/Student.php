<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'nis',
        'nism',
        'full_name',
        'gender',
        'birth_date',
        'class_level',
        'rombel',
        'entry_year',
        'status',
        'email',
        'phone',
        'address',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'class_level' => 'integer',
        'entry_year' => 'integer',
    ];

    // ─── Constants ───────────────────────────────────────────

    const STATUS_ACTIVE = 'active';

    const STATUS_GRADUATED = 'graduated';

    const STATUS_WITHDRAWN = 'withdrawn';

    const STATUS_INACTIVE = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_GRADUATED,
        self::STATUS_WITHDRAWN,
        self::STATUS_INACTIVE,
    ];

    const GENDERS = [
        'male' => 'Laki-laki',
        'female' => 'Perempuan',
    ];

    const CLASS_LEVELS = [7, 8, 9];

    const ROMBELS = ['1', '2', '3', '4'];

    /**
     * Dapatkan daftar pilihan rombel yang valid sesuai tingkat kelas.
     * Kelas 7: 7.1, 7.2, 7.3
     * Kelas 8: 8.1, 8.2, 8.3, 8.4
     * Kelas 9: 9.1, 9.2, 9.3, 9.4
     */
    public static function getRombelsForClass($classLevel): array
    {
        if (blank($classLevel)) {
            return [];
        }

        $level = (int) $classLevel;

        $existing = static::active()
            ->where('class_level', $level)
            ->whereNotNull('rombel')
            ->where('rombel', '!=', '')
            ->distinct()
            ->pluck('rombel')
            ->sort()
            ->values();

        if ($existing->isEmpty()) {
            $defaultRombels = match ($level) {
                7 => ['1', '2', '3'],
                8, 9 => ['1', '2', '3', '4'],
                default => ['1', '2', '3'],
            };
            $existing = collect($defaultRombels);
        }

        return $existing->mapWithKeys(fn ($r) => [$r => "Kelas {$level}.{$r}"])->all();
    }

    // ─── Relationships ───────────────────────────────────────

    /**
     * Orang tua/wali siswa.
     */
    public function parentProfile(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    /**
     * Histori penempatan akademik per tahun ajaran.
     */
    public function academicPlacements(): HasMany
    {
        return $this->hasMany(StudentAcademicYear::class);
    }

    /**
     * Tagihan siswa.
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Pembayaran siswa.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    /**
     * Hanya siswa aktif (target billing otomatis).
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Filter berdasarkan kelas.
     */
    public function scopeInClass($query, int $classLevel)
    {
        return $query->where('class_level', $classLevel);
    }

    /**
     * Filter berdasarkan rombel.
     */
    public function scopeInRombel($query, string $rombel)
    {
        return $query->where('rombel', $rombel);
    }

    // ─── Accessors ───────────────────────────────────────────

    /**
     * Menampilkan kelas dan rombel gabungan: "7.1"
     */
    public function getClassRombelAttribute(): string
    {
        return $this->rombel ? "{$this->class_level}.{$this->rombel}" : (string) ($this->class_level ?? '-');
    }

    // ─── Mutation Helpers ─────────────────────────────────────

    /**
     * Memproses mutasi keluar / pindah siswa.
     * Mengubah status menjadi withdrawn dan membatalkan tagihan belum lunas.
     */
    public function mutateOut(string $reason = '', ?string $date = null): int
    {
        $oldStatus = $this->status;
        $this->status = self::STATUS_WITHDRAWN;
        $this->save();

        $cancelledCount = 0;
        $pendingBills = $this->bills()
            ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->get();

        $cancellationNote = '[Dibatalkan - Siswa Pindah]'.($reason ? " Alasan: {$reason}" : '');

        foreach ($pendingBills as $bill) {
            if ($bill->paid_amount == 0) {
                $bill->status = Bill::STATUS_CANCELLED;
                $bill->outstanding_amount = 0;
                $bill->notes = trim(($bill->notes ? $bill->notes.' | ' : '').$cancellationNote);
                $bill->save();
                $cancelledCount++;
            } elseif ($bill->paid_amount > 0) {
                // Partial payment: tutup sisa tunggakan
                $bill->outstanding_amount = 0;
                $bill->notes = trim(($bill->notes ? $bill->notes.' | ' : '').$cancellationNote);
                $bill->save();
                $cancelledCount++;
            }
        }

        AuditLog::record(
            action: 'student_mutated_out',
            auditable: $this,
            oldValues: ['status' => $oldStatus],
            newValues: [
                'status' => self::STATUS_WITHDRAWN,
                'reason' => $reason,
                'date' => $date ?? now()->toDateString(),
                'cancelled_bills_count' => $cancelledCount,
            ]
        );

        return $cancelledCount;
    }

    /**
     * Mengembalikan status siswa mutasi menjadi aktif kembali.
     */
    public function revertMutation(): void
    {
        $oldStatus = $this->status;
        $this->status = self::STATUS_ACTIVE;
        $this->save();

        AuditLog::record(
            action: 'student_mutation_reverted',
            auditable: $this,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => self::STATUS_ACTIVE]
        );
    }
}
