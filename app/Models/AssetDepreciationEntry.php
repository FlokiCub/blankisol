<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDepreciationEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'year_month',
        'monthly_depreciation',
        'accumulated_depreciation',
    ];

    protected $casts = [
        'monthly_depreciation' => 'float',
        'accumulated_depreciation' => 'float',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
