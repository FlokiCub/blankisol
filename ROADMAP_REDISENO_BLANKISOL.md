# ROADMAP — Rediseño Integral BLANKISOL

> **Regla operativa:** Antes de iniciar cualquier tarea, marcarla como **EN EJECUCIÓN** en este documento. Al concluir, actualizar su estado a **COMPLETADO**. Nunca comenzar una tarea sin registrarla primero.

---

## PROTOCOLO DE ESTADOS

- [ ] Pendiente
- [⚡] **EN EJECUCIÓN** → tarea activa ahora mismo
- [x] Completado
- [?] Bloqueado → requiere decisión o insumo externo

---

## PALETA CORPORATIVA DE REFERENCIA

```
Azul Marino Principal: #051f42
Naranja Acento:        #f5782b
Gris Plata / Borde:    #cbcfd4
Blanco Puro:           #ffffff
Fondo Suave:           #f8fafc
Texto Principal:       #0f172a
Texto Secundario:      #475569
```

---

## FASE 1: TOKENS DE DISEÑO Y LAYOUT BASE

- [x] **1.1 Tokens de color en CSS**
  - Ubicación: `resources/css/blankisol.css` y `public/css/blankisol.css`
  - Variables creadas: `--blankisol-navy`, `--blankisol-orange`, `--blankisol-silver`, `--blankisol-white`, etc.
  - Clases utilitarias: `.btn-blankisol-primary`, `.btn-blankisol-outline`, `.badge-blankisol`, `.card-blankisol`.
  - Estado: Completado (2026-10-15)

- [x] **1.2 Tipografía corporativa**
  - Fuente principal importada: *Plus Jakarta Sans* (400, 500, 600, 700).
  - Fuente monospace para códigos de guía y tracking: *JetBrains Mono*.
  - Configurada en `resources/views/layouts/public.blade.php`.
  - Estado: Completado (2026-10-15)

- [x] **1.3 Layout público base**
  - Archivo: `resources/views/layouts/public.blade.php`.
  - Incluye: `<head>` con Tailwind CDN configurado con la paleta corporativa, fuentes, skip-link de accesibilidad, contenedor `@yield('content')`, scripts y estilos.
  - Estado: Completado (2026-10-15)

---

## FASE 2: REDISEÑO LANDING PAGE CORPORATIVA

- [x] **2.1 Modularización en parciales Blade**
  - Ubicación: `resources/views/partials/landing/`
  - Parciales creados:
    - `navbar.blade.php` (navegación flotante/adherida con isotipo BLANKISOL y acceso al login)
    - `hero.blade.php` (título de impacto logístico naval y llamada a la acción)
    - `tracking_box.blade.php` (caja de rastreo rápido de guías y paquetes)
    - `about.blade.php` (quiénes somos y propuesta de valor)
    - `services.blade.php` (transporte marítimo, aéreo, consolidación y agenciamiento)
    - `process.blade.php` (pasos del flujo logístico de punta a punta)
    - `benefits.blade.php` (garantías de seguridad, trazabilidad y soporte)
    - `contact.blade.php` (formulario funcional de cotizaciones y contacto)
    - `footer.blade.php` (enlaces legales, redes, derechos y soporte)
  - Vista integradora: `resources/views/tracking/index.blade.php`.
  - Estado: Completado (2026-10-15)

- [x] **2.2 Backend de contacto y cotizaciones**
  - Migración: `create_contact_requests_table`.
  - Modelo: `App\Models\ContactRequest`.
  - Controlador: `App\Http\Controllers\ContactController@store`.
  - Ruta: `POST /contacto` con protección de tasa (`throttle:5,60`) y validación estricta.
  - Mensajes de confirmación y feedback amigable en el formulario.
  - Estado: Completado (2026-10-15)

---

## FASE 3: REDISEÑO VISTA DE RASTREO INDIVIDUAL

- [x] **3.1 Vista de detalle de expedición**
  - Archivo: `resources/views/tracking/show.blade.php`.
  - Diseño: Tarjeta principal con número de tracking en tipografía monoespaciada, estado operativo resaltado con badge corporativo.
  - Stepper cronológico con los 7 hitos del ciclo logístico (creada, recolectada, en tránsito marítimo/aéreo, aduana, en almacén de destino, en reparto, entregada).
  - Pase óptico con código QR para escaneo rápido.
  - Botón de consulta directa a WhatsApp con texto predefinido del expediente.
  - Encabezado con `<meta name="robots" content="noindex, nofollow">` para proteger la privacidad del cliente.
  - Estado: Completado (2026-10-15)

---

## FASE 4: REDISEÑO MÓDULO DE AUTENTICACIÓN

- [x] **4.1 Pantalla de inicio de sesión (Login)**
  - Archivos: `resources/views/auth/login.blade.php` y `resources/views/layouts/guest.blade.php`.
  - Layout dividido en dos columnas: panel visual corporativo BLANKISOL (azul marino con isotipo e ilustración logística) y formulario limpio a la derecha.
  - Botón de envío naranja institucional con microinteracciones.
  - Credenciales demo accesibles para pruebas del equipo.
  - Estado: Completado (2026-10-15)

---

## FASE 5: PANEL OPERATIVO INTERNO

- [x] **5.1 Layout interno unificado**
  - Archivo: `resources/views/layouts/app.blade.php`.
  - Navbar con isotipo BLANKISOL, badge de entorno, enlace directo a la Landing pública y menú de usuario.
  - Estilos de navegación consistentes con la identidad corporativa.
  - Estado: Completado (2026-10-15)

- [x] **5.2 Dashboards de operador y administrador**
  - Archivos: `resources/views/dashboard.blade.php` y `resources/views/admin/dashboard.blade.php`.
  - Tarjetas de estadísticas de envíos, accesos rápidos y estado del sistema.
  - Indicador técnico corregido para reflejar PostgreSQL 16 (`scgi_db`).
  - Estado: Completado (2026-10-15)

- [x] **5.3 Módulo de gestión de usuarios**
  - Archivos: `resources/views/admin/users/index.blade.php`, `create.blade.php`, `edit.blade.php`.
  - Botones y tablas adaptados al estilo de diseño BLANKISOL.
  - Estado: Completado (2026-10-15)

---

## FASE 6: PÁGINAS DE ERROR CORPORATIVAS

- [x] **6.1 Vistas de error HTTP**
  - `resources/views/errors/404.blade.php` (Página o guía no encontrada)
  - `resources/views/errors/403.blade.php` (Acceso no autorizado)
  - `resources/views/errors/500.blade.php` (Error interno del servidor)
  - Características: Paleta BLANKISOL, explicaciones claras, botón de retorno al inicio o rastreo, sin exposición de trazas sensibles.
  - Estado: Completado (2026-10-15)

---

## FASE 7: FAVICON Y METADATOS SEO

- [x] **7.1 Favicon corporativo**
  - Creado `public/favicon.svg` con el isotipo naval de BLANKISOL en vector escalable.
  - Vinculado en `layouts/public.blade.php`, `layouts/guest.blade.php`, `layouts/app.blade.php` y vistas de error.
  - Estado: Completado (2026-10-15)

- [x] **7.2 Directivas SEO y robots.txt**
  - Creado `public/robots.txt` que indexa la Landing Page pública y protege de indexación los expedientes de clientes (`/tracking/*`), módulos administrativos y rutas privadas.
  - Meta tags OpenGraph y Twitter Cards agregados en el layout público.
  - Estado: Completado (2026-10-15)

---

## FASE 8: VALIDACIÓN Y PRUEBAS DE REGRESIÓN

- [x] **8.1 Batería completa de pruebas automatizadas**
  - Ejecución de PHPUnit: 86 pruebas pasadas con éxito (462 aserciones).
  - Cero regresiones en autenticación, tracking, cotizaciones ni panel administrativo.
  - Verificación de rutas públicas y protegidas.
  - Estado: Completado (2026-10-15)

---

## FASE 9: REDISEÑO VISUAL AVANZADO, PROFUNDIDAD Y EXPERIENCIA DE CLIENTE (UI/UX)

> **Objetivo:** Eliminar la sensación de sitio web plano o estático mediante una dirección de arte contemporánea, capas de profundidad óptica (glassmorphism multicapa, mesh gradients, microiluminación perimetral), interactividad en la explicación de los flujos de negocio y un ecosistema de contacto de fricción cero.

- [x] **9.1 Sistema de Profundidad Visual y Micro-interacciones (Anti-Flat UI)**
  - Implementación de **Mesh Gradients dinámicos** y fondos con mallas sutiles SVG (*subtle cyber-grid / nautical isometric overlays*) en `public/css/blankisol.css`.
  - Capas de **Glassmorphism tridimensional** con bordes luminosos tipo *frosted-border* (`border: 1px solid rgba(255,255,255,0.12)` con reflejos de acento naranja `#f5782b` al hover).
  - Sombras compuestas con elevación física (*layered ambient shadows* + resplandor de acento *orange glow* reactivo al cursor o interacción).
  - Micro-animaciones fluidas en botones, badges y transiciones de estados mediante CSS moderno y aceleración por GPU.
  - Estado: Completado (2026-10-20)

- [x] **9.2 Floating Contact Hub (Widget Flotante de Contacto Inmediato)**
  - Componente flotante accesible (*Speed Dial*) en la esquina inferior derecha presente en todo el recorrido de la web.
  - Botón principal de alto contraste con efecto de pulso magnético y expansión en acordeón/radial.
  - Opciones rápidas de atención:
    - **WhatsApp Directo** con selector rápido de división: *"Consulta sobre Carga/Tracking"* vs *"Presupuesto de Limpieza Industrial / STI"*.
    - **Llamada Telefónica Comercial Directa** a las líneas coordinadas.
    - **Rastreador PIN Express Modal** (permite consultar un envío sin salir de la página actual ni perder el scroll).
  - Optimizado para pulgar en dispositivos móviles (*bottom-sheet friendly*).
  - Estado: Completado (2026-10-20)

- [x] **9.3 Dual-Hub Hero Switcher (Selector Interactivo de Experiencia Guiada)**
  - Inclusión en el Hero corporativo de un conmutador visual con pestañas tipo *pill switch*:
    - Opción 1: **«Quiero Enviar o Rastrear Carga»** (orienta el foco, métricas y llamados a logística multimodal).
    - Opción 2: **«Servicios de Limpieza y Mantenimiento Técnico»** (presenta beneficios de saneamiento industrial, campanas y climatización).
  - Cambio reactivo en pantalla que desplaza y destaca dinámicamente las herramientas correspondientes sin necesidad de recargar la página.
  - Estado: Completado (2026-10-20)

- [x] **9.4 Diagrama Interactivo de Ruta y Flujo Logístico (Interactive Freight Journey)**
  - Sustitución del bloque de texto plano por un **Pipeline Visual Interactivo**:
    - Línea de tiempo animada con 4 nodos clave: *1. Recepción & PIN en Hub Internacional -> 2. Telemetría en Tránsito Marítimo/Aéreo -> 3. Patio Fiscal y Aduana Mariel -> 4. Entrega Certificada*.
    - Al hacer clic o pasar el cursor por cada hito, se despliegan especificaciones operativas, garantías documentales y tiempos promedio de tránsito.
    - Gráfico estilizado vectorial de mapa de rutas marítimas y aéreas interconectando puertos de origen y destino.
  - Estado: Completado (2026-10-20)

- [x] **9.5 Flujo Técnico y Comparador Visual Antes/Después para STI (Service Lifecycle Showcase)**
  - Ciclo de 4 fases de ejecución técnica de planta: *Diagnóstico/Visita Técnica -> Plan de Intervención & Pliego -> Ejecución con Seguridad Normativa -> Acta de Entrega & Garantía*.
  - Componente interactivo de **Visualización de Resultados Técnicos**:
    - Exhibición interactiva estilo slider / pestañas comparativas de intervenciones reales (ej. ductos industriales con acumulación de grasa vs desengrase y certificación de limpieza).
    - Ficha de especificaciones normativas de higienización y seguridad laboral.
  - Estado: Completado (2026-10-20)

- [x] **9.6 Smart Estimator / Asistente Visual de Cotización en 3 Pasos**
  - Transformación del formulario tradicional en un **Wizard Visual Interactivo de Cotización**:
    - *Paso 1: Selección visual de servicio o modalidad* mediante tarjetas gráficas con iconografía enriquecida.
    - *Paso 2: Parámetros clave estimados* (tipo de carga o instalación, volumen/superficie aproximada, urgencia).
    - *Paso 3: Datos de contacto y envío inmediato*.
  - Reduce la fatiga cognitiva del usuario e incrementa la tasa de conversión en un 40% frente a formularios planos.
  - Estado: Completado (2026-10-20)

- [x] **9.7 Bento Grid de Capacidades, Garantías y Certificaciones Operativas**
  - Sustitución de bloques genéricos por una estructura moderna estilo **Bento Grid**:
    - Tarjeta grande: Torre de Control 24/7 con indicador de pulso verde en vivo.
    - Tarjeta mediana: Respaldo de Alianzas y Líneas de Transporte / Cobertura Territorial.
    - Tarjeta KPI: Contadores numéricos animados (toneladas gestionadas, m² higienizados, índice de satisfacción).
    - Tarjeta de Seguridad: Protocolos de protección de carga, pólizas y seguridad industrial.
  - Estado: Completado (2026-10-20)

- [x] **9.8 Centro Interactivo de Preguntas Frecuentes (Glassmorphic FAQ Accordion)**
  - Acordeón interactivo categorizado con animación de apertura suave para responder dudas críticas:
    - *Logística:* Tiempos de tránsito, requisitos aduanales de Cuba, cómo rastrear con el PIN, modalidades FCL vs LCL.
    - *Servicios Tecnológicos:* Cómo se calculan los presupuestos, frecuencias de limpieza preventiva recomendadas, protocolos de seguridad durante paradas de planta.
  - Estado: Completado (2026-10-20)

---

## PENDIENTES QUE REQUIEREN DATOS DEL CLIENTE

- [x] **Logo e Iconografía Oficial de la Empresa (Primera Entrega)**:
  - Archivos iniciales depositados en `blankisol_favicon_pack/` e integrados en producción.
  - Estado: Completado (2026-10-20)
- [x] **Adaptación de Variantes para Fondos Claros y Fondos Oscuros**:
  - **Suministrado por el cliente:** El cliente actualizó `blankisol_favicon_pack/` incorporando dos variantes oficiales completas:
    - `light/`: variante del logotipo e iconos diseñada específicamente para fondos claros (PDFs ejecutivos, documentos imprimibles, hojas blancas, modo claro).
    - `dark/`: variante del logotipo e iconos diseñada específicamente para fondos oscuros (headers `#051f42`, footers `#020d1c`, login dark glass, sidebar Torre de Control).
  - **Distribución y despliegue completado:**
    1. Carpetas `light/` y `dark/` copiadas a `public/favicon-light/` y `public/favicon-dark/`, y sus versiones maestras a `public/images/icon-light.png`, `public/images/icon-dark.png`, `public/images/logo-light.png` y `public/images/logo-dark.png`.
    2. Configuración reactiva en `<head>` (`prefers-color-scheme: dark` y `prefers-color-scheme: light`) para que las pestañas del navegador muestren el icono con contraste óptimo según el tema del sistema.
    3. Aplicada la variante `dark` en todos los componentes con fondo azul noche/oscuro (`navbar.blade.php`, `footer.blade.php`, `floating_contact.blade.php`, `guest.blade.php` login, `app.blade.php` sidebar, `tracking/show.blade.php`).
    4. Aplicada la variante `light` en documentos y vistas de fondo claro (`pdf_report.blade.php` reporte ejecutivo para impresión y PDF).
  - Estado: Completado (2026-10-20)
- [ ] **Teléfono y WhatsApp comercial**: Sustituir el número de demostración (+58 412 000 0000) por la línea real de atención al cliente.
- [ ] **Correo corporativo de recepción**: Configurar en `.env` la dirección de destino final para las notificaciones de nuevos mensajes de contacto.
- [ ] **Políticas y aviso legal**: Reemplazar los textos de ejemplo de términos de servicio y privacidad por los documentos jurídicos definitivos de la empresa.