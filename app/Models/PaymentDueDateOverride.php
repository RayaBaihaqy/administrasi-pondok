<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentDueDateOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_type_id',
        'student_id',
        'due_day',
        'due_date',
    ];

    protected $casts = [
        'due_day' => 'integer',
        'due_date' => 'date',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
