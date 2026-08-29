<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentProfile extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit karena model name != table name.
     */
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'contact_email',
        'address',
    ];

    // ─── Relationships ───────────────────────────────────────

    /**
     * Akun user untuk login.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Siswa yang terhubung dengan orang tua ini.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}
