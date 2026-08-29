<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    const TYPE_PAYMENT_SUCCESS = 'pembayaran_sukses';
    const TYPE_DUE_REMINDER = 'pengingat_tagihan';
    const TYPE_NEW_BILL = 'tagihan_baru';
    const TYPE_OVERDUE = 'tagihan_menunggak';

    const STATUS_SENT = 'terkirim';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'gagal';

    protected $fillable = [
        'parent_id',
        'student_id',
        'recipient_name',
        'phone_number',
        'message_type',
        'amount',
        'message_content',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Helper URL direct link ke WhatsApp Web / Desktop (wa.me)
     */
    public function getWhatsAppUrlAttribute(): string
    {
        return \App\Services\WhatsAppAutomationService::createWhatsAppUrl(
            (string) $this->phone_number,
            (string) $this->message_content
        );
    }
}
