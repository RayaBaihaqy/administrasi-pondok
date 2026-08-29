<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_level',
        'rombel',
    ];

    protected $casts = [
        'class_level' => 'integer',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getClassRombelAttribute(): string
    {
        return $this->class_level.'-'.$this->rombel;
    }
}
