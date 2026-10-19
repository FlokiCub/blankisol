
---

## REGLA OPERATIVA (OBLIGATORIA EN TODOS LOS ROADMAPS)

> Antes de comenzar cualquier tarea de este documento:
> 1. Marcar su estado como **[?] EN EJECUCION**
> 2. Al concluir, marcar como **[x] Completado** con fecha de cierre
> 3. Si hay un bloqueo externo, marcar como **[?] Bloqueado** y documentar el motivo
> 4. Nunca declarar una tarea completada sin haberla ejecutado y verificado
> 5. Solo puede haber UNA tarea EN EJECUCION a la vez por desarrollador

---
# 📊 SCGI — Auditoría Técnica y Matriz de Control de Avance
> **Proyecto:** Sistema de Control y Gestión de Importaciones (SCGI)  
> **Stack Objetivo:** Laravel 11 / 12 + PostgreSQL (`scgi_db`, user: `postgres`, pass: `Leo123.l`) + FilamentPHP  
> **Fecha de Actualización:** Octubre 2026  
> **Rol Líder:** Lead Fullstack Developer & Arquitecto de Software  
> **Estado Global Estimado:** **100% de los Sprints 1.1, 1.2, 1.3, 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 4 y 5 Completados y Validados** (82 Tests Automatizados con 445 Aserciones en Verde)


---

## 🎯 1. Resumen Ejecutivo del Estado del Sistema

El sistema SCGI implementa fielmente el flujo real de las operaciones portuarias y logísticas:

1. **Infraestructura y Seguridad RBAC (Sprint 1.1 - COMPLETADO):**
   * Migración completa y validada a **PostgreSQL** (`scgi_db`).
   * Matriz de **7 Roles Corporativos** (`UserRole` Enum: `Director`, `Aduana`, `PVP_Tecnico`, `Despacho_Puerto`, `Logistica_Chofer`, `Cajero_Regional`, `Auditor`).
   * `RequireRoleMiddleware` protegiendo cada ruta según el principio de menor privilegio.
   * `RbacService` controlando la matriz de transiciones de estados del flujo de importación.
   * Campos de persistencia crítica en `equipments` (`duty_paid_abroad`, `delivery_paid_abroad`, `customs_entry_at`, `customs_cleared_at`, `pvp_certified_at`).

2. **El Núcleo Financiero y Control de Cajas (Sprint 1.2 - COMPLETADO):**
   * Ecosistema completo de Cajas Regionales (`CashRegister`), Turnos y Arqueos multimoneda (`CashShift`), y Libro Contable de Pagos (`Payment`).
   * Soporte multimoneda estricto **USD y CUP** con tasa de cambio oficial configurable (`ExchangeRate`).
   * **Regla de Negocio de Prepago Total en Origen:** Identificación inequívoca de productos prepagados en el exterior (`duty_paid_abroad` + `delivery_paid_abroad` o exento de domicilio), exonerando al cliente final de cualquier cobro en Cuba y habilitando su entrega inmediata.
   * **Bloqueo Estricto de Entrega por Deuda:** Si un equipo tiene aranceles o fletes pendientes, el sistema prohíbe su entrega (`canBeDelivered() === false`) hasta que el cajero registre el pago correspondiente en ventanilla o pasarela.
   * `PaymentService` y `CashierController` con transacciones de base de datos atómicas (`DB::transaction`).

3. **Inteligencia de Aranceles y Exención Fiscal (Sprint 1.3 - COMPLETADO):**
   * Motor arancelario [TariffService](file:///d:/XAMPP/htdocs/mi-web/app/Services/TariffService.php) con bifurcación estricta B2B vs B2C:
     - **B2C:** Tasa arancelaria del 10% USD sobre el valor declarado + $50 USD flete a domicilio (si no fue prepagado en origen).
     - **B2B:** 0% arancelario condicionado a la validación estricta de certificado aduanal, número de declaración y partida arancelaria (Capítulos 84, 85, 87, 90) vía XML o documentación de soporte. En caso de ausencia o certificado vencido (>1 año), aplica fallback 10% B2C con log de advertencia.
     - **Manifiestos XML:** Función de parseo `parseCustomsXmlForExemption()` para procesar manifiestos aduanales de SolveCargo.
     - **Integración Eloquent:** Método `calculateFromEquipment()`.

4. **Módulo de Aduanas y Semáforos Portuarios 48h / 72h (Sprint 2.1 - COMPLETADO):**
   * Creación de `CustomsInspectionService` para gobernar el flujo de arribo a puerto Mariel, inicio de inspección física y libramiento aduanal (`customs_cleared_at`).
   * Motor de Semáforos de Permanencia Portuaria basado en `customs_entry_at`:
     - 🟢 **Verde (<48h):** Operación en tiempo estándar.
     - 🟡 **Amarillo (48h - 72h):** Alerta preventiva de congestión portuaria.
     - 🔴 **Rojo (>72h):** Alerta crítica de retención con notificación prioritaria a Dirección.
   * Controlador `CustomsController` y vistas operativas con RBAC `UserRole::ADUANA` y `UserRole::DIRECTOR`.

5. **Escáner de Despacho y Cotejo con Manifiesto en Puerto Mariel (Sprint 2.2 - COMPLETADO):**
   * **Regla de Negocio de Despacho sin Ensamblaje:** En el área de despacho los productos **no se ensamblan**. Pasan únicamente por el escáner del código de barras, PIN o VIN para cotejar los datos del paquete físico con el manifiesto de carga (SolveCargo / BL).
   * **Cotejo y Pase Directo:** Al confirmar la coincidencia (`MATCH_CONFIRMED`), el equipo transiciona directamente a `READY_FOR_DISPATCH` (Listo para Despacho Regional / Destino Provincial).
   * Lógica implementada en `DispatchReceptionService`: `lookupManifestForScan()`, `verifyAndClearForDispatch()`, y `reportManifestDiscrepancy()`.
   * Interfaz operativa en `DispatchScannerController` y vista `resources/views/dispatch/scanner.blade.php` con previsualización en tiempo real del manifiesto, indicadores arancelarios (Prepagado $0.00 vs Cobro en Cuba), reporte de discrepancias y disparo automático de WhatsApp.

6. **Hojas de Ruta y Logística Regional (Sprint 2.3 - COMPLETADO):**
   * Gestión completa de Hojas de Ruta de distribución hacia almacenes provinciales (`RegionalDispatchRoute`).
   * Servicio `RegionalLogisticsService` con soporte para creación de rutas, escaneo/asignación por PIN/barcode, despacho en convoy (`REGIONAL_TRANSIT`) y recepción masiva o escaneo individual en destino (`IN_REGIONAL_WAREHOUSE`).
   * Interfaz de usuario con RBAC para `Director`, `Logistica_Chofer` y `Despacho_Puerto` en `LogisticsRouteController` y vistas Blade interactivas.

7. **Conexión Gateway Real WhatsApp Business API y Gateway Local QR (Sprint 2.4 - COMPLETADO):**
   * Arquitectura multicanal basada en drivers (`WhatsAppDriverInterface`, `MetaCloudWhatsAppDriver`, `LocalGatewayWhatsAppDriver`, `LogWhatsAppDriver`).
   * Sanitización inteligente de números telefónicos de Cuba (`5XXXXXXX` ➔ `535XXXXXXX`) y formato internacional E.164.
   * **Bypass de Restricciones Geográficas de Cuba:** Microservicio Node.js autónomo en `whatsapp-gateway/` basado en `@whiskeysockets/baileys` con panel visual web (`http://localhost:3333/qr`) para vinculación directa por código QR sin depender de Meta for Developers ni costos por plantilla.
   * Soporte para plantillas HSM oficiales de Meta por estado (`scgi_port_arrival`, `scgi_ready_for_dispatch`, `scgi_regional_transit`, `scgi_regional_arrival`, `scgi_equipment_delivered`).
   * Cola asíncrona con reintentos exponenciales en `SendWhatsAppNotificationJob`.
   * Webhook oficial de Meta Cloud API (`WhatsAppWebhookController` en `/api/webhooks/whatsapp`) con verificación GET Challenge y procesamiento POST de estados en tiempo real (`delivered`, `read`, `failed`).
   * Comando de consola dedicado: `php artisan whatsapp:test {phone}` con soporte multi-driver (`--driver=local`, `--driver=meta`, `--driver=log`).

8. **Generador de Reportes y Panel de Auditoría (Sprint 3.1 - COMPLETADO):**
   * Servicio `ReportService` con exportación CSV de Actas de Cierre/Arqueo de turnos, libro mayor de recaudación y KPIs.
   * Controlador `AuditController` protegido por RBAC (`Director` y `Auditor`).
   * Acta oficial imprimible y descargable de cierre de turno con resumen de diferencias multimoneda y firmas de responsabilidad.
   * Búsqueda de trazabilidad cronológica inmutable por PIN, VIN o SolveCargo ID con desglose de inspecciones, rutas, cobros y notificaciones WhatsApp.

9. **Parser y Carga Masiva de Manifiestos Excel / CSV 22 Columnas (Sprint 3.2 - COMPLETADO):**
   * Mapeo estricto e inteligente de las 22 columnas oficiales del modelo Excel: `ESTADO`, `No CONTENEDOR`, `No CHASSIS`, `No MOTOR`, `COLOR`, `MARCA`, `MODELO`, `AÑO DE FABRICACION`, `TIPO DE ALIMENTACION`, `PAIS DE ORIGEN`, `CODIGO DE ENVIO`, `CONTENIDO`, `FECHA DE CARGA`, `PRIMER NOMBRE`, `SEGUNDO NOMBRE`, `PRIMER APELLIDO`, `SEGUNDO APELLIDO`, `CI`, `PAS`, `No TELEFONO`, `PROVINCIA`, `OBSERVACIONES`.
   * Soporte multiformato (`.xlsx`, `.xls`, `.csv`) vía `PhpSpreadsheet v5`.
   * Algoritmo de dos pases (exacto y por límite de palabras) para resolución infalible de cabeceras evitando colisiones de subcadenas.
   * Detección y deducción automática del tipo de equipo (`VEHICLE`, `SOLAR_SYSTEM`, `BATTERY_PACK`, `ACCESSORY`).
   * Asignación automática de Almacén Regional por provincia del destinatario.
   * Generación de PINs únicos (`BK-XXXXX`) e importación transaccional con opción de disparo inmediato de notificaciones WhatsApp.
   * Interfaz con Drag & Drop, previsualización de cotejo antes de importar y descarga de plantilla oficial `.xlsx`.

10. **Rediseño Visual de Vanguardia y Experiencia de Usuario (UI/UX Premium) (Sprint 4 — COMPLETADO 100%):**
    * **Sistema de Diseño Unificado (Design System & Tokens):** Adopción de tipografía corporativa de vanguardia (*Plus Jakarta Sans* para jerarquía y títulos, *Inter* para lectura operativa densa y *JetBrains Mono* para códigos VIN, PINs, tracking IDs, montos y tasas de cambio).
    * **Paleta HSL y Estética Glassmorphism:** Reemplazo de grises planos por canvas espacial profundo (`#060911` / `#0b0f19`), tarjetas de vidrio esmerilado con desenfoque (`backdrop-blur-xl bg-slate-900/75`), bordes con sutil resplandor translúcido y micro-animaciones de estado.
    * **Identidad de Marca Homogénea:** Erradicación total de textos residuales de plantillas genéricas; consagración de la identidad corporativa **BLANKISOL SCGI — Torre de Control Logística**.
    * **UX Industrial y Escaneo Multisensorial:** Retroalimentación acústica mediante *Web Audio API* (síntesis de tonos armónicos para coincidencia y alerta para discrepancias) y destello de color visual (*flash animation*) para ambientes de muelle y almacén ruidosos, con *autofocus lock* perpetuo para pistolas láser de códigos de barra.
    * **Layout Responsivo y Mobile-First:** Barra de navegación lateral colapsable y menú *off-canvas* para operadores en tablets y dispositivos móviles de campo, complementado con atajo rápido `Ctrl+K`.
    * **Optimización de Impresión Física (`@media print`):** Formato limpio de alta resolución para actas de turno, recibos de pago y hojas de ruta sin fondos oscuros que gasten tinta.
    * **Pase Óptico de Retiro:** Generación dinámica de código QR en la ficha de tracking público para retiro ágil en ventanilla y desglose de liquidación arancelaria ($0.00 Prepagado vs Cobro en Cuba).

---

## 📋 2. Matriz de Implementación por Módulos

| # | Funcionalidad del Negocio | Estado | Archivo(s) Involucrado(s) | Diagnóstico y Alcance Actual |
|---|---|---|---|---|
| **1** | **Infraestructura PostgreSQL** | **Completado (100%)** | `.env`, `config/database.php`, Migraciones | Base de datos `scgi_db` en PostgreSQL con soporte nativo para UUIDs, JSONB y tipado estricto. |
| **2** | **Seguridad: RBAC 7 Perfiles** | **Completado (100%)** | `UserRole.php`, `RbacService.php`, `RequireRoleMiddleware.php`, `User.php` | Control de acceso por rol jerárquico, sucursal regional y matriz de transiciones de estado. |
| **3** | **Persistencia de Equipos y Aduana** | **Completado (100%)** | `equipments` table, `Equipment.php`, `EquipmentStatus.php` | 11 estados de flujo, campos `duty_paid_abroad`, `delivery_paid_abroad`, timestamps de aduana y PVP. |
| **4** | **Cajas Regionales y Turnos (USD/CUP)** | **Completado (100%)** | `CashRegister.php`, `CashShift.php`, `PaymentService.php`, `CashierController.php` | Apertura de turno con fondo inicial, cobros multimoneda y cierre con arqueo físico y detección de faltantes/sobrantes. |
| **5** | **Libro de Pagos y Liquidación** | **Completado (100%)** | `Payment.php`, `PaymentService.php`, `Equipment.php` | Métodos `cash_usd`, `cash_cup`, `transfer_cup`, `online_usd`, generación de número de recibo único y liquidación de equipos. |
| **6** | **Regla de Prepago Total vs Cobro en Cuba** | **Completado (100%)** | `Equipment.php`, `PaymentService.php`, `TariffService.php` | Diferenciación automática: $0 USD para prepagos desde origen vs cobro obligatorio previo a entrega para aranceles pendientes. |
| **7** | **Tracking Público por PIN / VIN** | **Completado (100%)** | `PublicTrackingController.php`, vistas Blade públicas | Búsqueda pública por PIN único de 8 caracteres, VIN y SolveCargo ID con historial cronológico inmutable. |
| **8** | **Inteligencia de Aranceles (B2B vs B2C)** | **Completado (100%)** | `TariffService.php`, `Sprint13TariffIntelligenceTest.php` | Exención fiscal validada por XML/declaración en B2B (0%) vs 10% USD para B2C, y parsing de manifiestos XML. |
| **9** | **Módulo de Aduana: Alertas 48h / 72h** | **Completado (100%)** | `CustomsInspectionService.php`, `CustomsController.php`, vistas | Semáforo de permanencia aduanal en puerto basado en `customs_entry_at`, libramiento y retención. |
| **10** | **Despacho: Escaneo y Cotejo con Manifiesto** | **Completado (100%)** | `DispatchReceptionService.php`, `DispatchScannerController.php`, vistas | Escaneo de código de bulto, cotejo instantáneo con manifiesto SolveCargo, pase directo a Despacho sin ensamblaje. |
| **11** | **Hojas de Ruta y Logística Regional** | **Completado (100%)** | `RegionalDispatchRoute.php`, `RegionalLogisticsService.php`, `LogisticsRouteController.php` | Asignación de choferes, camiones, despacho en convoy y recepción en destino provincial por escaneo. |
| **12** | **Conexión Real WhatsApp (Meta API & Gateway Local QR)** | **Completado (100%)** | `WhatsAppNotificationService.php`, `LocalGatewayWhatsAppDriver.php`, `MetaCloudWhatsAppDriver.php`, `whatsapp-gateway/server.js` | Driver híbrido: soporte Meta Cloud API oficial y Gateway Local autónomo Node/Baileys (`http://localhost:3333/qr`) para evadir bloqueos de Meta en Cuba, comandos CLI de prueba y telemetría en BD. |
| **13** | **Reportes Financieros, Arqueos y Auditoría** | **Completado (100%)** | `ReportService.php`, `AuditController.php`, vistas `audit/` | Exportación CSV/PDF de arqueos, libro de pagos multimoneda y trazabilidad inmutable de equipos. |
| **14** | **Parser y Carga Masiva de Manifiestos Excel / CSV** | **Completado (100%)** | `ManifestImportService.php`, `ManifestImportController.php`, vistas `manifests/` | Procesamiento de 22 columnas exactas del modelo Excel, asignación provincial de almacenes, deducción de tipo de equipo y PINs automáticos. |
| **15** | **Integración SolveCargo: API dayready/v1** | **Standby (Esperando API Keys)** | `SolveCargoApiService.php` | Sincronización asíncrona de despachos vía Cron/Scheduler al recibir credenciales oficiales. |
| **16** | **Rediseño Visual de Vanguardia & UX Premium** | **Completado (100%)** | `layouts/app.blade.php`, `layouts/guest.blade.php`, todas las vistas de módulos | Elevación de todas las interfaces a un estándar estético de primer nivel mundial: tipografías de impacto, glassmorphism, feedback acústico/visual en escáneres, navegación responsiva y ergonomía operativa. |
| **17** | **Motor de Factibilidad y Rentabilidad Económica (Sprint 5)** | **Completado (100%)** | `ContainerProfitabilityService.php`, `ContainerProfitabilityController.php`, `economic_parameters`, `fuel_and_tariff_rates`, vistas `profitability/` | Costos de 4 etapas (Puerto, Transitaria, Regional escalonado, Comercialización y Tributos Ley 113), Margen de Contribución ($MC$), Punto de Equilibrio ($Kg_{eq}$), Veredictos de Despacho y Simulador Interactivo de Sensibilidad. |

---

## 📈 3. Registro de Estado y Tareas (Checklist Vivo)

- [x] **Sprint 1.1:** Configuración de PostgreSQL (`scgi_db`), corrección de `equipments` y despliegue del sistema RBAC (7 roles) con Middlewares.
- [x] **Sprint 1.2:** Creación del ecosistema de Cajas Regionales, Turnos y tabla de Pagos multimoneda (USD/CUP) con regla estricta de productos prepagados.
- [x] **Sprint 1.3:** Refactorización total de `TariffService` para manejar la diferencia real entre B2B (XML de exención) y B2C (+10% USD).
- [x] **Sprint 2.1:** Módulo de Aduanas con alertas de semáforo 48h / 72h basadas en `customs_entry_at`.
- [x] **Sprint 2.2:** Escáner de Despacho y Cotejo con Manifiesto en Puerto Mariel (Sin ensamblaje; cotejo estricto de código/bulto y pase directo a Listo para Despacho Regional / Destino).
- [x] **Sprint 2.3:** Hojas de Ruta y Logística Regional (Creación de hoja de ruta, asignación de chofer/camión, escáner de carga, despacho en convoy y recepción en almacén provincial).
- [x] **Sprint 2.4:** Conexión Gateway Real WhatsApp Business API (Meta Cloud API, HSM templates, webhooks de entrega/lectura, cola de reintentos y logging de resiliencia).
- [x] **Gateway Local WhatsApp QR / Baileys (Bypass Bloqueo Meta en Cuba):** Microservicio local en Node.js (`whatsapp-gateway/server.js`) en puerto `3333`, panel web de vinculación QR (`/qr`), driver `LocalGatewayWhatsAppDriver` y comando `php artisan whatsapp:test` validado.
- [x] **Sprint 3.1:** Generador de Reportes y Panel Avanzado de Auditoría (Exportaciones PDF/CSV de Cierres de Turnos/Arqueos, Libro Contable de Pagos USD/CUP, Auditoría de Trazabilidad e Historial Inmutable para Perfil `Auditor` y `Director`).
- [x] **Sprint 3.2:** Parser y Carga Masiva de Manifiestos Excel / CSV (Mapeo exacto de las 22 columnas oficiales del modelo, deducción automática de tipos de equipos, asignación de almacén regional por provincia y generación de PINs únicos).
- [x] **Auditoría de Ciberseguridad & Hardening Aplicado:** Rate limiting (throttle) en login y tracking público, verificación de firma HMAC-SHA256 y hash_equals en webhooks de WhatsApp, bloqueo pesimista `lockForUpdate()` contra condiciones de carrera en cajas, y mitigación XXE con `LIBXML_NONET`.
- [x] **Sprint 4 (COMPLETADO 100%): Rediseño Visual de Vanguardia & UX Premium:**
  - [x] **Fase 4.1:** Layout Maestro & Design System Unificado (`layouts/app.blade.php`, `layouts/guest.blade.php`, `auth/login.blade.php`): tipografía *Plus Jakarta Sans* + *JetBrains Mono*, sidebar responsiva con drawer móvil, header enriquecido con estado de almacén y atajo `Ctrl+K`.
  - [x] **Fase 4.2:** Ergonomía y Feedback en Escáneres Industriales (`dispatch/scanner.blade.php`, `logistics/routes/show.blade.php`): retroalimentación de audio armónica vía Web Audio API (D5-A5 en acierto, tono grave en error), flash visual perimetral de pantalla, y autofocus lock perpetuo para pistolas ópticas en muelle.
  - [x] **Fase 4.3:** Módulos Operativos de Aduana & Hojas de Ruta (`customs/index.blade.php`, `logistics/routes/index.blade.php`): semáforos portuarios luminosos 48h/72h, cards de convoy en tránsito con indicadores visuales y filtros rápidos instantáneos.
  - [x] **Fase 4.4:** Módulos Financieros, Cajas y Reportes (`cashier/index.blade.php`, `cashier/receipt.blade.php`): terminal de caja multimoneda USD/CUP, arqueo dinámico y comprobante oficial con firmas de responsabilidad y reglas de impresión limpia (`@media print`).
  - [x] **Fase 4.5:** Portal de Tracking Público & Carga de Manifiestos (`tracking/show.blade.php`, `manifests/import.blade.php`): pase óptico QR dinámico para retiro en ventanilla, indicador de prepago $0.00 y zona Drag & Drop para las 22 columnas Excel.
- [x] **Sprint 5 (COMPLETADO 100%): Motor de Factibilidad y Rentabilidad Económica de Contenedores y Rutas:**
  - [x] **Fase 5.1:** Persistencia de Parámetros Económicos y Tarifas Vigentes (`economic_parameters`, `fuel_and_tariff_rates`, seeders con datos oficiales de CUPET, transitarias y normas técnicas de consumo L/viaje).
  - [x] **Fase 5.2:** Servicio Central de Inteligencia Financiera (`ContainerProfitabilityService.php`): cálculo de costos de 4 etapas (Puerto, Transitaria, Transporte Regional escalonado, Comercialización y Tributos Ley 113), determinación del Margen de Contribución ($MC$), Utilidad Neta proyectada y Punto de Equilibrio en peso ($Kg_{eq}$).
  - [x] **Fase 5.3:** Motor de Toma de Decisiones y Veredictos de Despacho (🟢 Factible con utilidades / 🟡 Marginal / 🔴 No Factible a pérdida con cálculo exacto de déficit en USD y kilogramos faltantes para justificar salida del camión).
  - [x] **Fase 5.4:** Interfaz de Evaluación de Viabilidad en Creación de Hojas de Ruta (`logistics/routes/create.blade.php` y `logistics/routes/show.blade.php`): tarjeta interactiva de factibilidad que advierte al despachador antes de emitir la orden si el convoy viaja con carga insuficiente.
  - [x] **Fase 5.5:** Dashboard Ejecutivo de Rentabilidad por Región & Simulador de Escenarios (Occidente vs Centro vs Oriente, sensibilidad al precio del diésel y exportación de informes).
  - [x] **Fase 5.6:** Suite de Pruebas Automatizadas basada en los casos de prueba oficiales del Excel (Contenedor 01, 03 y 05) en `Sprint5ContainerProfitabilityTest.php`.
- [ ] **Sprint 3.3:** Integración SolveCargo API (`dayready/v1`) al disponer de credenciales oficiales.

---
*Bitácora técnica actualizada por el Lead Fullstack Developer & Especialista en Análisis Económico y Logística Financiera.*


