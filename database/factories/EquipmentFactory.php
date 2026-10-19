<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BusinessType;
use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Models\Equipment;
use App\Models\RegionalWarehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $declaredValue = fake()->randomFloat(2, 800, 12000);
        $dutyAmount = round($declaredValue * 0.10, 2);

        return [
            'tracking_pin' => 'BK-' . fake()->unique()->numerify('#####'),
            'vin_serial' => 'VIN-' . strtoupper(Str::random(12)),
            'solve_cargo_tracking_id' => 'SC-' . date('Y') . '-MIA-' . fake()->unique()->numerify('#####'),
            'equipment_type' => fake()->randomElement(EquipmentType::cases()),
            'business_type' => fake()->randomElement(BusinessType::cases()),
            'brand' => fake()->randomElement(['Yadea', 'Huawei', 'BYD', 'NIO', 'Growatt', 'Taizhou']),
            'model' => fake()->randomElement(['G5 Pro', 'Smart Inverter 5kW', 'Yuan Plus', 'LUNA 10kWh', 'Titan 125']),
            'color' => fake()->randomElement(['Blanco', 'Negro Mate', 'Azul Titanio', 'Rojo Carmín', 'Gris Plata']),
            'current_status' => EquipmentStatus::PORT_ARRIVAL,
            'customs_status' => 'pending',
            'declared_value_usd' => $declaredValue,
            'duty_amount_usd' => $dutyAmount,
            'shipping_fee_usd' => 50.00,
            'home_delivery_requested' => true,
            'duty_paid_abroad' => false,
            'delivery_paid_abroad' => false,
            'is_fully_paid' => false,
            'customs_entry_at' => now()->subDay(),
            'customs_cleared_at' => null,
            'pvp_certified_at' => null,
            'delivered_at' => null,
            'current_warehouse_id' => null,
            'current_location_note' => 'Puerto Mariel — Terminal de Carga',
            'client_data' => [
                'name' => fake()->name(),
                'phone' => '+53 5 ' . fake()->numerify('### ####'),
                'delivery_address' => fake()->address(),
            ],
            'technical_specs' => [
                'power' => '3000W',
                'battery' => '72V 32Ah Litio',
            ],
        ];
    }

    /**
     * Indicate that the equipment is ready for regional dispatch.
     */
    public function readyForDispatch(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => EquipmentStatus::READY_FOR_DISPATCH,
            'customs_cleared_at' => now()->subHours(12),
            'pvp_certified_at' => now()->subHours(6),
            'current_location_note' => 'Área de Despacho Regional — Pallet Listo',
        ]);
    }

    /**
     * Indicate that the equipment is in customs inspection.
     */
    public function inCustoms(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => EquipmentStatus::CUSTOMS_INSPECTION,
            'customs_status' => 'in_inspection',
            'current_location_note' => 'Área de Aforo Físico — Aduana Mariel',
        ]);
    }

    /**
     * Indicate that the equipment is in regional transit.
     */
    public function inRegionalTransit(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => EquipmentStatus::REGIONAL_TRANSIT,
            'customs_cleared_at' => now()->subDays(1),
            'pvp_certified_at' => now()->subHours(18),
            'current_location_note' => 'En Tránsito Provincial en Camión',
        ]);
    }

    /**
     * Indicate that the equipment is stored in a regional warehouse.
     */
    public function inRegionalWarehouse(?RegionalWarehouse $warehouse = null): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => EquipmentStatus::IN_REGIONAL_WAREHOUSE,
            'current_warehouse_id' => $warehouse?->id,
            'current_location_note' => $warehouse ? "Almacén Regional {$warehouse->name}" : 'Almacén Regional',
        ]);
    }

    /**
     * Indicate that the equipment is fully prepaid abroad.
     */
    public function prepaidAbroad(): static
    {
        return $this->state(fn (array $attributes) => [
            'duty_paid_abroad' => true,
            'delivery_paid_abroad' => true,
            'is_fully_paid' => true,
            'duty_amount_usd' => 0.00,
            'shipping_fee_usd' => 0.00,
        ]);
    }

    /**
     * Indicate that the equipment is delivered to customer.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => EquipmentStatus::DELIVERED,
            'delivered_at' => now(),
            'is_fully_paid' => true,
            'current_location_note' => 'Entregado al Cliente Final',
        ]);
    }
}
