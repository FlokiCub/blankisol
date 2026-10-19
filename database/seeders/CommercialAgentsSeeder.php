<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CommercialAgent;
use Illuminate\Database\Seeder;

class CommercialAgentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = [
            // 5 Gestores Centrales
            ['agent_number' => 1, 'name' => 'Gestor Central 01', 'group' => 'Grupo central', 'territory' => 'Oficina central', 'region' => 'General', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 2, 'name' => 'Gestor Central 02', 'group' => 'Grupo central', 'territory' => 'Oficina central', 'region' => 'General', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 3, 'name' => 'Gestor Central 03', 'group' => 'Grupo central', 'territory' => 'Oficina central', 'region' => 'General', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 4, 'name' => 'Gestor Central 04', 'group' => 'Grupo central', 'territory' => 'Oficina central', 'region' => 'General', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 5, 'name' => 'Gestor Central 05', 'group' => 'Grupo central', 'territory' => 'Oficina central', 'region' => 'General', 'monthly_fixed_fee' => 50.00],

            // 16 Gestores Provinciales
            ['agent_number' => 6, 'name' => 'Gestor Pinar del Río', 'group' => 'Provincial', 'territory' => 'Pinar del Río', 'region' => 'Occidente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 7, 'name' => 'Gestor Artemisa', 'group' => 'Provincial', 'territory' => 'Artemisa', 'region' => 'Occidente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 8, 'name' => 'Gestor La Habana', 'group' => 'Provincial', 'territory' => 'La Habana', 'region' => 'Occidente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 9, 'name' => 'Gestor Mayabeque', 'group' => 'Provincial', 'territory' => 'Mayabeque', 'region' => 'Occidente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 10, 'name' => 'Gestor Matanzas', 'group' => 'Provincial', 'territory' => 'Matanzas', 'region' => 'Occidente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 11, 'name' => 'Gestor Isla de la Juventud', 'group' => 'Provincial', 'territory' => 'Isla de la Juventud', 'region' => 'Occidente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 12, 'name' => 'Gestor Cienfuegos', 'group' => 'Provincial', 'territory' => 'Cienfuegos', 'region' => 'Centro', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 13, 'name' => 'Gestor Villa Clara', 'group' => 'Provincial', 'territory' => 'Villa Clara', 'region' => 'Centro', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 14, 'name' => 'Gestor Sancti Spíritus', 'group' => 'Provincial', 'territory' => 'Sancti Spíritus', 'region' => 'Centro', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 15, 'name' => 'Gestor Ciego de Ávila', 'group' => 'Provincial', 'territory' => 'Ciego de Ávila', 'region' => 'Centro', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 16, 'name' => 'Gestor Camagüey', 'group' => 'Provincial', 'territory' => 'Camagüey', 'region' => 'Centro', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 17, 'name' => 'Gestor Las Tunas', 'group' => 'Provincial', 'territory' => 'Las Tunas', 'region' => 'Oriente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 18, 'name' => 'Gestor Holguín', 'group' => 'Provincial', 'territory' => 'Holguín', 'region' => 'Oriente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 19, 'name' => 'Gestor Granma', 'group' => 'Provincial', 'territory' => 'Granma', 'region' => 'Oriente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 20, 'name' => 'Gestor Santiago de Cuba', 'group' => 'Provincial', 'territory' => 'Santiago de Cuba', 'region' => 'Oriente', 'monthly_fixed_fee' => 50.00],
            ['agent_number' => 21, 'name' => 'Gestor Guantánamo', 'group' => 'Provincial', 'territory' => 'Guantánamo', 'region' => 'Oriente', 'monthly_fixed_fee' => 50.00],
        ];

        foreach ($agents as $agent) {
            CommercialAgent::updateOrCreate(
                ['agent_number' => $agent['agent_number']],
                [
                    'name' => $agent['name'],
                    'group' => $agent['group'],
                    'territory' => $agent['territory'],
                    'region' => $agent['region'],
                    'monthly_fixed_fee' => $agent['monthly_fixed_fee'],
                    'commission_mode' => '% DEL INGRESO',
                    'is_active' => true,
                    'notes' => 'Estructura comercial de ventas 2026',
                ]
            );
        }
    }
}
