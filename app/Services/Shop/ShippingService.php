<?php

declare(strict_types=1);

namespace App\Services\Shop;

use App\Models\Shop\ShopShippingRate;
use App\Models\Shop\ShopShippingZone;
use Illuminate\Support\Collection;

class ShippingService
{
    /**
     * Localiza la zona de entrega que cubre una provincia de Cuba.
     */
    public function resolveZoneForProvince(?string $province): ?ShopShippingZone
    {
        if (empty($province)) {
            // Zona por defecto o primera zona activa general
            return ShopShippingZone::where('is_active', true)->first();
        }

        $provinceTrimmed = trim($province);

        try {
            $zone = ShopShippingZone::where('is_active', true)
                ->whereNotNull('provinces')
                ->whereJsonContains('provinces', $provinceTrimmed)
                ->first();

            if ($zone) {
                return $zone;
            }
        } catch (\Throwable) {
            // Continuar con búsqueda en memoria si el motor SQL falla en casting JSON
        }

        // Si no coincide exactamente, busca por coincidencia insensible a mayúsculas
        $allZones = ShopShippingZone::where('is_active', true)->get();
        foreach ($allZones as $z) {
            if (!empty($z->provinces) && is_array($z->provinces)) {
                foreach ($z->provinces as $p) {
                    if (strcasecmp($p, $provinceTrimmed) === 0) {
                        return $z;
                    }
                }
            }
        }

        // Si ninguna coincide específicamente, retorna zona general o fallback
        return ShopShippingZone::where('is_active', true)
            ->whereNull('provinces')
            ->first() ?? ShopShippingZone::where('is_active', true)->first();
    }

    /**
     * Obtiene las tarifas disponibles en USD para una provincia dada.
     */
    public function getAvailableRatesForProvince(?string $province): Collection
    {
        $zone = $this->resolveZoneForProvince($province);

        if (!$zone) {
            return collect();
        }

        return ShopShippingRate::where('zone_id', $zone->id)
            ->where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();
    }

    /**
     * Valida y calcula la tarifa de entrega oficial en USD.
     */
    public function calculateShippingFee(?int $rateId, ?string $province): array
    {
        if (!$rateId) {
            return [
                'valid' => false,
                'rate' => null,
                'shipping_fee' => 0.00,
                'estimated_days' => 'A determinar',
                'reason' => 'No se ha seleccionado modalidad de envío.',
            ];
        }

        /** @var ShopShippingRate|null $rate */
        $rate = ShopShippingRate::with('zone')->where('id', $rateId)->where('is_active', true)->first();

        if (!$rate) {
            return [
                'valid' => false,
                'rate' => null,
                'shipping_fee' => 0.00,
                'estimated_days' => 'No disponible',
                'reason' => 'La tarifa de entrega seleccionada ya no está disponible.',
            ];
        }

        return [
            'valid' => true,
            'rate' => $rate,
            'shipping_fee' => round((float) $rate->price, 2),
            'estimated_days' => $rate->estimated_days ?? '3 a 5 días hábiles',
            'zone_name' => $rate->zone?->name ?? 'General',
            'reason' => 'Tarifa de entrega confirmada en USD.',
        ];
    }
}
