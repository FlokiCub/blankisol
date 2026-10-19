<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'value',
        'unit',
        'category',
        'description',
    ];

    /**
     * Get parameter value cast as float or string.
     */
    public function getParsedValueAttribute(): float|string
    {
        return is_numeric($this->value) ? (float) $this->value : (string) $this->value;
    }

    /**
     * Helper to retrieve value by code with fallback.
     */
    public static function getValue(string $code, mixed $default = null): mixed
    {
        $param = static::where('code', $code)->first();
        if (!$param) {
            return $default;
        }

        return is_numeric($param->value) ? (float) $param->value : $param->value;
    }
}
