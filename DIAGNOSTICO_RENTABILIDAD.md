# 🔍 Diagnóstico Técnico — Módulo de Rentabilidad de Contenedores (Paquetería)

> **Fecha:** Octubre 2026  
> **Proyecto:** SCGI BLANKISOL  
> **Referencia:** `Analisis_Rentabilidad_Paqueteria.xlsx` + `Guia_de_Uso_Rentabilidad_Paqueteria.pdf`

---

## 1. Mi Rol en Este Módulo

Actúo como **Arquitecto de Software Senior, Desarrollador Laravel Experto, Analista Financiero Especializado en Costes/Rentabilidad y Consultor de Gestión Económica Logística**. Mi comportamiento en cada paso será:

1. **Nunca inventar fórmulas financieras** — Cada cálculo debe trazarse al Excel de referencia.
2. **Reutilizar antes que duplicar** — Usar los contenedores, equipments, rutas y usuarios existentes.
3. **Preguntar ante ambigüedad** — Si una fórmula del Excel es ambigua, documentarlo y solicitar decisión.
4. **Implementar por fases** — Cada fase se valida antes de avanzar a la siguiente.
5. **Mantener trazabilidad** — Todo cálculo económico debe registrar qué parámetros y tarifas usó.
6. **No romper lo existente** — Cada cambio pasa por los 82 tests + 445 aserciones actuales en verde.
7. **Calcular en servidor** — Nunca confiar en cálculos del navegador para datos financieros.

---

## 2. Estado Actual del Sprint 5 (Lo Que YA Existe)

### 2.1 Tablas Creadas (Migración `2026_10_12_000001`)
| Tabla | Columnas | Estado |
|---|---|---|
| `economic_parameters` | code, name, value, unit, category, description | ✅ Funcional |
| `fuel_and_tariff_rates` | 14 campos de tarifas con `effective_from` | ✅ Funcional |
| `fixed_costs_monthly` | year_month, general/occ/cen/ori_fixed_usd | ✅ Básico |
| `container_profitability_analyses` | ~40 columnas de resultados + JSONB breakdown | ✅ Funcional |

### 2.2 Servicio de Cálculo (`ContainerProfitabilityService.php` — 567 líneas)
- ✅ Motor de 4 etapas: Puerto, Transitaria, Distribución Regional, Comisiones
- ✅ Ingresos por región (Occ/Cen/Ori) × tarifa/kg
- ✅ Normas técnicas cuando faltan datos reales
- ✅ Prorrateo de gastos fijos por kg
- ✅ Tributos: 10% ventas + 1% territorial + 35% utilidades
- ✅ Margen de contribución, punto de equilibrio, tarifa mínima
- ✅ Veredicto de decisión (FACTIBLE/MARGINAL/NO_FACTIBLE)
- ✅ Alertas de combustible y pérdidas regionales
- ✅ Simulador de sensibilidad (matriz diésel × tarifa)
- ✅ Evaluación de viabilidad de rutas de despacho

### 2.3 Controlador y Vistas
- ✅ `ContainerProfitabilityController` — CRUD + simulador + CSV export
- ✅ Vistas: `index`, `create`, `show`, `simulator`
- ✅ Rutas protegidas por RBAC (`director`, `auditor`)

### 2.4 Tests
- ✅ `Sprint5ContainerProfitabilityTest.php` — validación de cálculos vs Excel

---

## 3. GAP Analysis — Lo Que FALTA vs Requisitos del PDF/Excel

### 🔴 CRÍTICO — No Existe

| # | Funcionalidad Requerida | Impacto |
|---|---|---|
| G1 | **Rol `Jefe_Economico`** — El PDF exige un rol específico. Solo existen `director` y `auditor` con acceso. | Alto |
| G2 | **Ciclo económico del contenedor** (Abierto→En Análisis→Cerrado Económicamente) separado del estado operativo | Alto |
| G3 | **Gastos fijos desglosados por concepto** — Hoy solo hay 4 totales mensuales (general/occ/cen/ori). El Excel tiene ~30 conceptos individuales con centro de coste | Alto |
| G4 | **Activos y depreciación** — Tabla de vehículos, equipos con vida útil, valor residual, depreciación lineal mensual | Alto |
| G5 | **Gestores comerciales (21)** — 5 centrales + 16 provinciales con comisiones fijas+variables y conciliación | Alto |
| G6 | **Cierre mensual controlado** — Checklist de verificación, estados abierto/cerrado, trazabilidad de cierre | Alto |
| G7 | **Historial de tarifas congelado por análisis** — Al cerrar un contenedor, se deben preservar los valores usados. Hoy `applied_parameters` solo guarda telemetría de combustible | Medio |
| G8 | **Escenarios de simulación persistidos** — Guardar con nombre, fecha, usuario, supuestos. Hoy el simulador es efímero | Medio |
| G9 | **Alertas económicas persistidas** — Hoy las alertas son un array in-memory. Deben almacenarse y poder investigarse/justificarse | Medio |
| G10 | **Tributos sobre bases salariales** (14% SS + 5% fuerza de trabajo) — El Excel los aplica sobre nóminas, no sobre ingresos. Hoy NO se calculan | Alto |
| G11 | **Costes fijos no absorbidos** — Si un mes tiene gastos fijos pero pocos contenedores, la diferencia entre gasto total y gasto asignado debe mostrarse | Medio |
| G12 | **Conciliación comercial** — Comparar lo declarado por gestores vs ingresos de contenedores | Alto |
| G13 | **Resumen mensual (RESUMEN_MES)** — Vista consolidada mes a mes con acumulados | Alto |
| G14 | **Dashboard de dirección económica** con gráficos de evolución, estructura de costes, rentabilidad por región | Alto |
| G15 | **Exportación PDF** de informes (solo existe CSV) | Medio |
| G16 | **Desgaste del vehículo en distribución regional** — Hoy solo se aplica en puerto. El Excel lo aplica también en distribución | Medio |
| G17 | **Auditoría de cambios en análisis económicos** — quién modifica, cuándo, por qué | Alto |
| G18 | **Vinculación directa con contenedores existentes** — Hoy `container_id` es un string libre. Debería vincular con los contenedores reales de `equipments` | Crítico |

### 🟡 PARCIAL — Existe Pero Incompleto

| # | Funcionalidad | Estado Actual | Lo Que Falta |
|---|---|---|---|
| P1 | Parámetros económicos | 20 parámetros en seeder | Faltan ~10 del Excel (Km distribución por región, SS%, fuerza trabajo%) |
| P2 | Gastos fijos mensuales | 4 campos totalizados | Desglose por ~30 conceptos individuales con código y centro de coste |
| P3 | Tarifas históricas | Existe tabla con `effective_from` | Falta congelar snapshot al cerrar análisis |
| P4 | Ficha económica del contenedor | Vista `show` con desglose | Faltan bloques de control/documentación, datos faltantes, historial |
| P5 | Simulador | Funcional en memoria | Falta persistencia de escenarios, comparación real vs simulado |
| P6 | Alertas | Array in-memory por contenedor | Faltan persistencia, investigación, justificación documental |

---

## 4. Mapeo de Datos Existentes Reutilizables

### Tabla `equipments` → Datos del Contenedor
| Campo Equipment | Uso en Rentabilidad |
|---|---|
| `id` (UUID) | Vinculación directa al análisis económico |
| `solve_cargo_tracking_id` | Identificador del contenedor/BL |
| `customs_entry_at` | Fecha de arribo al puerto |
| `equipment_type` | Tipo de contenedor/equipo |
| `current_status` | Estado operativo |
| `delivered_at` | Fecha de finalización de distribución |
| `current_warehouse_id` → `RegionalWarehouse` | Región/provincia de destino |
| `client_data` (JSONB) | Provincia del destinatario → región |

### Tabla `regional_dispatch_routes` → Distribución
| Campo | Uso en Rentabilidad |
|---|---|
| `destination_warehouse_id` | Determina región (Occ/Cen/Ori) |
| `equipments` (pivot) | Equipos en cada ruta → kg estimados |
| `dispatched_at` / `received_at` | Fechas reales de distribución |

### Tabla `regional_warehouses` → Regiones
| Campo | Uso en Rentabilidad |
|---|---|
| `name` | Mapeo a Occidente/Centro/Oriente |
| `province` | Clasificación regional |

### Tabla `users` → Roles y Auditoría
| Campo | Uso en Rentabilidad |
|---|---|
| `role` (UserRole enum) | Control RBAC del módulo económico |
| `regional_warehouse_id` | Asignación regional del usuario |

---

## 5. Arquitectura Propuesta de Nuevas Entidades

```
equipment (existente)
    │
    └──► container_economic_analysis (1:1 por período)
              │
              ├──► economic_cost_details (1:N por etapa)
              ├──► economic_alerts (1:N)
              └──► economic_audit_log (1:N)

fuel_and_tariff_rates (existente, se amplía)
    └──► rate_snapshots (congelado al cerrar análisis)

fixed_cost_items (NUEVO — reemplaza fixed_costs_monthly)
    └──► fixed_cost_monthly_entries (valor real por mes)

assets (NUEVO — vehículos, equipos depreciables)
    └──► asset_depreciation_entries (mensual automático)

commercial_agents (NUEVO — 21 gestores)
    └──► commercial_liquidations (mensual)

monthly_closings (NUEVO — cierre mensual controlado)

simulation_scenarios (NUEVO — escenarios persistidos)
```

---

*Continúa en `ROADMAP_RENTABILIDAD_PAQUETERIA.md`*
