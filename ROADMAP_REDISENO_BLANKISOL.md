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

## PENDIENTES QUE REQUIEREN DATOS DEL CLIENTE

- [ ] **Logo SVG vectorial final**: Reemplazar el isotipo estilizado actual cuando el cliente entregue el archivo oficial definitivo de la marca.
- [ ] **Teléfono y WhatsApp comercial**: Sustituir el número de demostración (+58 412 000 0000) por la línea real de atención al cliente.
- [ ] **Correo corporativo de recepción**: Configurar en `.env` la dirección de destino final para las notificaciones de nuevos mensajes de contacto.
- [ ] **Políticas y aviso legal**: Reemplazar los textos de ejemplo de términos de servicio y privacidad por los documentos jurídicos definitivos de la empresa.