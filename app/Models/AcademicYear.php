<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

    /**
     * Penempatan siswa pada tahun ajaran ini.
     */
    public function placements(): HasMany
    {
        return $this->hasMany(StudentAcademicYear::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    /**
     * Tahun ajaran yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Static Helpers ──────────────────────────────────────

    /**
     * Ambil tahun ajaran aktif saat ini.
     */
    public static function current(): ?self
    {
        return static::active()->first();
    }

    /**
     * Set tahun ajaran ini sebagai aktif dan nonaktifkan yang lain.
     * Memastikan hanya 1 tahun ajaran yang aktif.
     */
    public function setAsActive(): void
    {
        // Nonaktifkan semua tahun ajaran lain
        static::where('id', '!=', $this->id)->update(['is_active' => false]);

        // Aktifkan tahun ajaran ini
        $this->update(['is_active' => true]);
    }
}
