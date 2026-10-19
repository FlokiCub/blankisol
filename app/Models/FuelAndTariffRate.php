<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelAndTariffRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'effective_from',
        'tariff_occidente_usd_kg',
        'tariff_centro_usd_kg',
        'tariff_oriente_usd_kg',
        'diesel_price_usd_l',
        'port_appointment_usd',
        'driver_diet_usd',
        'labor_shift_transitaria_usd',
        'labor_shift_transitory_center_usd',
        'transitaria_service_enci_usd',
        'transitaria_service_av_usd',
        'commercial_commission_pct',
        'commercial_commission_usd_kg',
        'vehicle_wear_usd_km',
        'support_document_note',
        'is_active',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'tariff_occidente_usd_kg' => 'float',
        'tariff_centro_usd_kg' => 'float',
        'tariff_oriente_usd_kg' => 'float',
        'diesel_price_usd_l' => 'float',
        'port_appointment_usd' => 'float',
        'driver_diet_usd' => 'float',
        'labor_shift_transitaria_usd' => 'float',
        'labor_shift_transitory_center_usd' => 'float',
        'transitaria_service_enci_usd' => 'float',
        'transitaria_service_av_usd' => 'float',
        'commercial_commission_pct' => 'float',
        'commercial_commission_usd_kg' => 'float',
        'vehicle_wear_usd_km' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Get active rate applicable for a given date.
     */
    public static function getRateForDate(?string $date = null): ?self
    {
        $targetDate = $date ?? now()->toDateString();

        return static::where('effective_from', '<=', $targetDate)
            ->where('is_active', true)
            ->orderBy('effective_from', 'desc')
            ->first();
    }

    /**
     * Get effective rates, falling back to default model parameters.
     */
    public static function getEffectiveRates(?string $date = null): self
    {
        $rate = static::getRateForDate($date);
        if ($rate) {
            return $rate;
        }

        return static::firstOrCreate(
            ['is_active' => true],
            [
                'effective_from' => '2026-01-01',
                'tariff_occidente_usd_kg' => 3.70,
                'tariff_centro_usd_kg' => 3.70,
                'tariff_oriente_usd_kg' => 3.70,
                'diesel_price_usd_l' => 2.00,
                'port_appointment_usd' => 50.00,
                'driver_diet_usd' => 45.00,
                'labor_shift_transitaria_usd' => 20.00,
                'labor_shift_transitory_center_usd' => 20.00,
                'transitaria_service_enci_usd' => 0.08,
                'transitaria_service_av_usd' => 0.08,
                'commercial_commission_pct' => 0.03,
                'commercial_commission_usd_kg' => 0.05,
                'vehicle_wear_usd_km' => 0.12,
                'support_document_note' => 'Tarifas base estándar',
            ]
        );
    }
}
