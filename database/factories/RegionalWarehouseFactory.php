<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RegionalWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegionalWarehouse>
 */
class RegionalWarehouseFactory extends Factory
{
    protected $model = RegionalWarehouse::class;

    public function definition(): array
    {
        $province = fake()->randomElement(['La Habana', 'Santiago de Cuba', 'Holguín', 'Camagüey', 'Matanzas', 'Pinar del Río']);
        $code = strtoupper(substr($province, 0, 3)) . '-' . fake()->unique()->numerify('##');

        return [
            'code' => $code,
            'name' => "Almacén Regional {$province}",
            'province' => $province,
            'address' => fake()->streetAddress() . ", {$province}",
            'contact_phone' => '+53 ' . fake()->numerify('## ### ###'),
            'is_active' => true,
        ];
    }
}
