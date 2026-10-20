<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case DIRECTOR = 'director';
    case ADUANA = 'aduana';
    case PVP_TECNICO = 'pvp_tecnico';
    case DESPACHO_PUERTO = 'despacho_puerto';
    case LOGISTICA_CHOFER = 'logistica_chofer';
    case CAJERO_REGIONAL = 'cajero_regional';
    case AUDITOR = 'auditor';
    case JEFE_ECONOMICO = 'jefe_economico';
    case ADMINISTRADOR_TIENDA = 'administrador_tienda';
    case CLIENTE_TIENDA = 'cliente_tienda';

    public function label(): string
    {
        return match ($this) {
            self::DIRECTOR => 'Dirección General (Torre de Control)',
            self::ADUANA => 'Inspector / Operador de Aduana',
            self::PVP_TECNICO => 'Técnico de Patio / Ensamblaje PVP',
            self::DESPACHO_PUERTO => 'Operador de Despacho (Puerto Mariel)',
            self::LOGISTICA_CHOFER => 'Chofer / Logística de Distribución',
            self::CAJERO_REGIONAL => 'Cajero / Almacén Regional',
            self::AUDITOR => 'Auditor de Trazabilidad e Historial',
            self::JEFE_ECONOMICO => 'Jefe Económico / Finanzas y Rentabilidad',
            self::ADMINISTRADOR_TIENDA => 'Administrador Comercial de Tienda (BLANKISOL SHOP)',
            self::CLIENTE_TIENDA => 'Cliente de BLANKISOL SHOP',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DIRECTOR => 'Acceso ejecutivo total, métricas, alertas críticas y configuraciones.',
            self::ADUANA => 'Gestión de arribos a puerto, inspección aduanal y semáforos 48h/72h.',
            self::PVP_TECNICO => 'Inspección técnica, ensamblaje BOM, test de batería y puesta a punto.',
            self::DESPACHO_PUERTO => 'Escaneo de recepción de contenedores y vinculación inicial de manifiestos.',
            self::LOGISTICA_CHOFER => 'Ejecución y traslado en hojas de ruta provinciales.',
            self::CAJERO_REGIONAL => 'Cobro y liquidación de aranceles y fletes en USD y CUP por almacén.',
            self::AUDITOR => 'Inspección de auditoría, trazabilidad inmutable y logs.',
            self::JEFE_ECONOMICO => 'Gestión económica de contenedores, costos fijos, tarifas, liquidaciones y cierre mensual.',
            self::ADMINISTRADOR_TIENDA => 'Gestión exclusiva del catálogo, pedidos, inventario y ventas de la tienda virtual.',
            self::CLIENTE_TIENDA => 'Compras en la tienda virtual, seguimiento de pedidos y gestión de cuenta personal.',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_column(
            array_map(fn (self $role) => ['value' => $role->value, 'label' => $role->label()], self::cases()),
            'label',
            'value'
        );
    }
}
