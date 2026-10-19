
---

## REGLA OPERATIVA (OBLIGATORIA EN TODOS LOS ROADMAPS)

> Antes de comenzar cualquier tarea de este documento:
> 1. Marcar su estado como **[?] EN EJECUCION**
> 2. Al concluir, marcar como **[x] Completado** con fecha de cierre
> 3. Si hay un bloqueo externo, marcar como **[?] Bloqueado** y documentar el motivo
> 4. Nunca declarar una tarea completada sin haberla ejecutado y verificado
> 5. Solo puede haber UNA tarea EN EJECUCION a la vez por desarrollador

---
# 📋 ROADMAP — Módulo Análisis de Rentabilidad de Contenedores (Paquetería)

> **Referencia:** `DIAGNOSTICO_RENTABILIDAD.md`  
> **Estado:** Sprint 6 — Extensión del Motor Económico

---

## Fase 6.1 — Modelo de Datos y Migraciones

- [x] **6.1.1** Crear rol `Jefe_Economico` en `UserRole` enum (Completado 2026-10-15)
- [x] **6.1.2** Migración: tabla `fixed_cost_items` (código, nombre, centro_coste, clasificación, es_salario, valor_base_mensual) (Completado 2026-10-15)
- [x] **6.1.3** Migración: tabla `fixed_cost_monthly_entries` (fixed_cost_item_id, year_month, importe_real, observaciones, documentos) (Completado 2026-10-15)
- [x] **6.1.4** Migración: tabla `assets` (descripción, tipo, fecha_alta, valor_adquisición, valor_residual, vida_útil, fecha_baja, mantenimiento_mensual, seguros_anuales) (Completado 2026-10-15)
- [x] **6.1.5** Migración: tabla `asset_depreciation_entries` (asset_id, year_month, depreciación_mensual, acumulada) (Completado 2026-10-15)
- [x] **6.1.6** Migración: tabla `commercial_agents` (nombre, grupo, territorio, región, pago_fijo_mensual, modalidad_comisión) (Completado 2026-10-15)
- [x] **6.1.7** Migración: tabla `commercial_liquidations` (agent_id, year_month, base_variable, comisión_calculada, total_pagar) (Completado 2026-10-15)
- [x] **6.1.8** Migración: tabla `commercial_reconciliations` (year_month, importe_gestores, importe_operaciones, diferencia, estado, observaciones) (Completado 2026-10-15)
- [x] **6.1.9** Migración: tabla `monthly_closings` (year_month, estado, checklist_json, cerrado_por, cerrado_at, notas) (Completado 2026-10-15)
- [x] **6.1.10** Migración: tabla `simulation_scenarios` (nombre, usuario, fecha, supuestos_json, resultados_json) (Completado 2026-10-15)
- [x] **6.1.11** Migración: tabla `economic_alerts` (analysis_id, tipo, severidad, descripción, investigado, justificación, resuelto_por) (Completado 2026-10-15)
- [x] **6.1.12** Migración: tabla `economic_audit_log` (entidad, entidad_id, acción, datos_anteriores, datos_nuevos, usuario_id) (Completado 2026-10-15)
- [x] **6.1.13** Modificar `container_profitability_analyses`: agregar `economic_status` (abierto/en_revision/cerrado), `closed_by`, `closed_at`, `reopened_reason`, `applied_rates_snapshot` (JSONB completo) (Completado 2026-10-15)
- [x] **6.1.14** Agregar parámetros faltantes al seeder: `Pct_SS` (0.14), `Pct_Fuerza_Trabajo` (0.05), `Km_Dist_OCC`, `Km_Dist_CEN`, `Km_Dist_ORI`, etc. (Completado 2026-10-15)
- [x] **6.1.15** Crear modelos Eloquent con relaciones para todas las tablas nuevas (Completado 2026-10-15)
- [x] **6.1.16** Crear seeders con los ~30 conceptos de gastos fijos del Excel (Completado 2026-10-15)
- [x] **6.1.17** Crear seeder con los 21 gestores comerciales del Excel (Completado 2026-10-15)

**Criterio de aceptación:** `php artisan migrate` exitoso, modelos con relaciones, seeders ejecutables.

---

## Fase 6.2 — Motor de Cálculo Extendido

- [x] **6.2.1** Agregar desgaste del vehículo en distribución regional (hoy solo se aplica en puerto) (Completado 2026-10-15)
- [x] **6.2.2** Implementar tributos salariales: 14% SS + 5% fuerza de trabajo sobre bases salariales (NO sobre ingresos) (Completado 2026-10-15)
- [x] **6.2.3** Calcular gastos fijos desde `fixed_cost_items` + `fixed_cost_monthly_entries` en vez de los 4 totales (Completado 2026-10-15)
- [x] **6.2.4** Calcular depreciación mensual automática desde tabla `assets` (Completado 2026-10-15)
- [x] **6.2.5** Implementar gastos fijos NO absorbidos (total mes − suma asignada a contenedores) (Completado 2026-10-15)
- [x] **6.2.6** Calcular comisiones de los 21 gestores por modalidad (% ingreso vs $/kg) (Completado 2026-10-15)
- [x] **6.2.7** Implementar conciliación: gestores declarado vs operaciones registradas (Completado 2026-10-15)
- [x] **6.2.8** Congelar snapshot completo de tarifas+parámetros al cerrar análisis económico (Completado 2026-10-15)
- [x] **6.2.9** Implementar margen de seguridad: (kg_real − kg_equilibrio) / kg_real × 100 (Completado 2026-10-15)
- [x] **6.2.10** Validar fórmulas contra hoja CALCULO del Excel (~95 columnas) (Completado 2026-10-15)

**Criterio:** Resultados de prueba reproducen los valores del Excel con tolerancia ≤ $0.50.

---

## Fase 6.3 — Integración con Contenedores Existentes

- [x] **6.3.1** Vincular `container_profitability_analyses` a `equipments` por grupo de `solve_cargo_tracking_id` (contenedor real) (Completado 2026-10-15)
- [x] **6.3.2** Crear vista de listado de contenedores disponibles para análisis (agrupados desde `equipments`) (Completado 2026-10-15)
- [x] **6.3.3** Auto-recuperar datos operativos: fecha arribo, tipo, provincia destino, transitaria, estado (Completado 2026-10-15)
- [x] **6.3.4** Calcular kg por región desde `regional_dispatch_routes` + `regional_warehouses` (Completado 2026-10-15)
- [x] **6.3.5** Marcar datos económicos faltantes que no existen en el módulo operativo (Completado 2026-10-15)
- [x] **6.3.6** Implementar estado económico separado del estado operativo (Completado 2026-10-15)
- [x] **6.3.7** Precálculo automático al vincular contenedor (Etapa 1 del ciclo económico) (Completado 2026-10-15)

**Criterio:** El jefe económico selecciona contenedores existentes sin reintroducir datos.

---

## Fase 6.4 — Gestión Económica y Configuración

- [x] **6.4.1** CRUD de `fixed_cost_items` con centro de coste y clasificación (Completado 2026-10-15)
- [x] **6.4.2** Registro mensual de importes reales por concepto de gasto fijo (Completado 2026-10-15)
- [x] **6.4.3** CRUD de `assets` con cálculo automático de depreciación lineal (Completado 2026-10-15)
- [x] **6.4.4** CRUD de `commercial_agents` (21 gestores) (Completado 2026-10-15)
- [x] **6.4.5** Liquidación mensual de gestores con cálculo de comisión (Completado 2026-10-15)
- [x] **6.4.6** Pantalla de conciliación comercial mensual (Completado 2026-10-15)
- [x] **6.4.7** Administración de parámetros económicos (edición con historial) (Completado 2026-10-15)
- [x] **6.4.8** Administración de tarifas históricas (nueva versión sin alterar anteriores) (Completado 2026-10-15)
- [x] **6.4.9** Implementar cierre mensual con checklist de 12 verificaciones (Completado 2026-10-15)
- [x] **6.4.10** Bloqueo de mes cerrado + reapertura autorizada con trazabilidad (Completado 2026-10-15)

**Criterio:** Todos los parámetros del Excel son configurables desde la interfaz web.

---

## Fase 6.5 — Dashboard y Reportes

- [x] **6.5.1** Dashboard principal con los 18+ indicadores del requisito (filtrable por período, región, transitaria, estado) (Completado 2026-10-15)
- [x] **6.5.2** Gráfico de evolución mensual (ingresos, costes, utilidad, margen) (Completado 2026-10-15)
- [x] **6.5.3** Gráfico de estructura de costes por categoría y etapa (Completado 2026-10-15)
- [x] **6.5.4** Comparativa de rentabilidad por región (Occ vs Cen vs Ori) (Completado 2026-10-15)
- [x] **6.5.5** Vista RESUMEN_MES con acumulados y comparación entre meses (Completado 2026-10-15)
- [x] **6.5.6** Ficha económica completa del contenedor (5 bloques: general, carga, costes, resultado, control) (Completado 2026-10-15)
- [x] **6.5.7** Listado tabular con filtros, búsqueda, ordenación y paginación (Completado 2026-10-15)
- [x] **6.5.8** Exportación PDF de informes con empresa, período, filtros, indicadores (Completado 2026-10-15)
- [x] **6.5.9** Exportación Excel de resultados filtrados (Completado 2026-10-15)
- [x] **6.5.10** Alertas visibles con semáforo y posibilidad de investigar/justificar (Completado 2026-10-15)

**Criterio:** El dashboard responde preguntas del requisito funcional (sección 2 del prompt).

---

## Fase 6.6 — Simulador de Escenarios Persistente

- [⏳] **6.6.1** Formulario de simulación con todas las variables del Excel SIMULADOR (EN EJECUCIÓN)
- [ ] **6.6.2** Persistencia de escenarios (nombre, fecha, usuario, supuestos, resultados)
- [ ] **6.6.3** Comparación visual: escenario real vs simulado vs diferencia
- [ ] **6.6.4** Tarifa mínima de equilibrio por región
- [ ] **6.6.5** Sensibilidad de utilidad frente a variaciones de tarifa y diésel
- [ ] **6.6.6** Botón "Restaurar valores reales" en el formulario
- [ ] **6.6.7** Garantía de integridad: simulación NUNCA modifica datos reales

**Criterio:** Escenarios guardados y consultables sin afectar contabilidad real.

---

## Fase 6.7 — Permisos, Auditoría y Seguridad

- [ ] **6.7.1** Middleware RBAC para `Jefe_Economico` en todas las rutas del módulo
- [ ] **6.7.2** Políticas de autorización Laravel (Policies) por entidad económica
- [ ] **6.7.3** Log de auditoría para crear/modificar/cerrar/reabrir análisis
- [ ] **6.7.4** Protección de análisis cerrados (solo modificable con permiso explícito)
- [ ] **6.7.5** Validación del lado servidor de todos los cálculos
- [ ] **6.7.6** Transacciones DB para operaciones de cierre
- [ ] **6.7.7** Prevención de duplicados (un análisis por contenedor+período)

---

## Fase 6.8 — Tests y Validación vs Excel

- [ ] **6.8.1** Test: 4 contenedores de ejemplo → utilidad acumulada ≈ $9,168 USD
- [ ] **6.8.2** Test: Contenedor de prueba cierre → consumo 520L vs norma 450L → desviación 16%
- [ ] **6.8.3** Test: Contenedor de prueba → utilidad antes de impuestos = $2,806.24 USD
- [ ] **6.8.4** Test: Contenedor sin carga en una región
- [ ] **6.8.5** Test: Normas técnicas cuando faltan datos reales
- [ ] **6.8.6** Test: Cambio de tarifa con vigencia posterior (no retroactiva)
- [ ] **6.8.7** Test: Mes con gastos fijos y sin contenedores
- [ ] **6.8.8** Test: Costes fijos no absorbidos
- [ ] **6.8.9** Test: Depreciación activo alta/baja
- [ ] **6.8.10** Test: Conciliación comercial con diferencias
- [ ] **6.8.11** Test: Escenario simulado con pérdidas
- [ ] **6.8.12** Test: División entre cero y períodos sin datos
- [ ] **6.8.13** Test: Reapertura autorizada de período cerrado
- [ ] **6.8.14** Test: Permisos RBAC del módulo

---

## Orden de Implementación Recomendado

```
Fase 6.1 (Datos)  ──►  Fase 6.2 (Motor)  ──►  Fase 6.3 (Integración)
                                                       │
                            Fase 6.4 (Gestión)  ◄──────┘
                                   │
                            Fase 6.5 (Dashboard)
                                   │
                            Fase 6.6 (Simulador)
                                   │
                       Fase 6.7 (Permisos/Auditoría)
                                   │
                         Fase 6.8 (Tests/Validación)
```

**Total: ~80 tareas en 8 fases.**

---
*Documento generado como guía de implementación incremental. Cada tarea se marca ✅ al completarse.*

