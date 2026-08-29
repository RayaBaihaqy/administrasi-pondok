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

    const ROMBELS = ['1', '2', '3', '4', '5', 'A', 'B', 'C', 'D', 'E', 'F'];

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
        return $this->class_level.'.'.$this->rombel;
    }
}
