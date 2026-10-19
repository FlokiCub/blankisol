<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_month',
        'status',
        'checklist',
        'closed_by',
        'closed_at',
        'notes',
        'reopen_reason',
        'reopened_by',
        'reopened_at',
    ];

    protected $casts = [
        'checklist' => 'array',
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
    ];

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function reopener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    public function isClosed(): bool
    {
        return $this->status === 'cerrado';
    }
}
