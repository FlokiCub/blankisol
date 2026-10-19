<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppNotification extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'whatsapp_notifications';

    protected $fillable = [
        'equipment_id',
        'recipient_phone',
        'recipient_name',
        'event_trigger',
        'message_body',
        'status',
        'provider',
        'template_name',
        'gateway_message_id',
        'error_details',
        'raw_response',
        'sent_at',
        'delivered_at',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'raw_response' => 'array',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}
