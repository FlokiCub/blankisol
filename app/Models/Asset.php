<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_number',
        'description',
        'asset_type',
        'registration_date',
        'acquisition_value',
        'residual_value',
        'useful_life_years',
        'deregistration_date',
        'monthly_maintenance',
        'annual_insurance',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'deregistration_date' => 'date',
        'acquisition_value' => 'float',
        'residual_value' => 'float',
        'useful_life_years' => 'integer',
        'monthly_maintenance' => 'float',
        'annual_insurance' => 'float',
        'is_active' => 'boolean',
    ];

    public function depreciationEntries(): HasMany
    {
        return $this->hasMany(AssetDepreciationEntry::class);
    }

    /**
     * Calcula la depreciación mensual lineal teórica según el Excel:
     * Depreciación Anual = (Valor adquisición - Valor residual) / Vida útil
     * Depreciación Mensual = Depreciación Anual / 12
     */
    public function getTheoreticalMonthlyDepreciation(): float
    {
        if ($this->useful_life_years <= 0) {
            return 0.0;
        }

        $depreciableBase = max(0, $this->acquisition_value - $this->residual_value);
        return round(($depreciableBase / $this->useful_life_years) / 12, 2);
    }

    /**
     * Calcula la depreciación mensual total de todos los activos activos para un mes dado.
     */
    public static function calculateTotalMonthlyDepreciation(string $yearMonth): float
    {
        $startDate = $yearMonth . '-01';
        $endDate = \Illuminate\Support\Carbon::parse($startDate)->endOfMonth()->toDateString();

        $activeAssets = static::where('is_active', true)
            ->where(function ($query) use ($startDate) {
                $query->whereNull('deregistration_date')
                    ->orWhere('deregistration_date', '>=', $startDate);
            })
            ->where('registration_date', '<=', $endDate)
            ->get();

        $total = 0.0;
        foreach ($activeAssets as $asset) {
            $total += $asset->getTheoreticalMonthlyDepreciation();
        }

        return round($total, 2);
    }

    /**
     * Registra o actualiza las entradas de depreciación para el mes dado en asset_depreciation_entries.
     *
     * @return array<int, AssetDepreciationEntry>
     */
    public static function generateMonthlyDepreciationEntries(string $yearMonth): array
    {
        $startDate = $yearMonth . '-01';
        $endDate = \Illuminate\Support\Carbon::parse($startDate)->endOfMonth()->toDateString();

        $activeAssets = static::where('is_active', true)
            ->where(function ($query) use ($startDate) {
                $query->whereNull('deregistration_date')
                    ->orWhere('deregistration_date', '>=', $startDate);
            })
            ->where('registration_date', '<=', $endDate)
            ->get();

        $entries = [];
        foreach ($activeAssets as $asset) {
            $monthly = $asset->getTheoreticalMonthlyDepreciation();
            $priorAccum = (float) AssetDepreciationEntry::where('asset_id', $asset->id)
                ->where('year_month', '<', $yearMonth)
                ->sum('monthly_depreciation');
            $accum = round($priorAccum + $monthly, 2);

            $entry = AssetDepreciationEntry::updateOrCreate(
                ['asset_id' => $asset->id, 'year_month' => $yearMonth],
                [
                    'monthly_depreciation' => $monthly,
                    'accumulated_depreciation' => $accum,
                ]
            );
            $entries[] = $entry;
        }

        return $entries;
    }
}
