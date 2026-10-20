# BLANKISOL SURL — IMPLEMENTACIÓN DE SERVICIOS TECNOLÓGICOS INTEGRALES (STI)
## Documento de Entrega y Roadmap Técnico-Comercial

---

### 1. RESUMEN EJECUTIVO

Se ha completado la evolución de la plataforma web de **BLANKISOL SURL** (Laravel 12 + PostgreSQL) para consolidar su identidad corporativa dual, integrando con igual jerarquía sus dos divisiones operativas:

1. **Logística y Trazabilidad:** Envíos marítimos, aéreos, terrestres, gestión aduanal y torre de control de rastreo en tiempo real mediante PIN/contenedor.
2. **Servicios Tecnológicos Integrales (STI):** Limpieza industrial técnica, higienización especializada, desengrase de sistemas de extracción y cocinas industriales, desincrustación con vapor, desobstrucción hidrosanitaria, climatización y mantenimiento constructivo.

---

### 2. ARQUITECTURA Y ARCHIVOS MODIFICADOS / CREADOS

#### A. Base de Datos y Modelos
- **Migración:** `database/migrations/2026_10_19_174009_create_tech_service_requests_table.php`
  - Crea la tabla `tech_service_requests` con campos estructurados: `nombre`, `empresa`, `email`, `telefono`, `servicio_interes`, `tipo_instalacion`, `ubicacion`, `superficie_volumen`, `nivel_complejidad`, `descripcion`, `plazo_deseado`, `ip_address`, `aceptado_privacidad`.
- **Modelo:** `app/Models/TechServiceRequest.php`
  - Definido con `$fillable` seguro, cast para `aceptado_privacidad` booleano y timestamps.

#### B. Controladores y Rutas
- **Rutas Web (`routes/web.php`):**
  - `GET /servicios-tecnologicos` → `tech_services.index` (`TechServiceController@index`).
  - `POST /servicios-tecnologicos/solicitar` → `tech_services.quote.store` (`TechServiceController@storeQuoteRequest`) con middleware `throttle:10,60`.
- **Controlador (`app/Http/Controllers/TechServiceController.php`):**
  - `index()`: Suministra `$categories` y `$sectors` a la vista comercial.
  - `storeQuoteRequest()`: Validación estricta con mensajes personalizados en español, protección anti-spam por campo trampa (*honeypot* `empresa_url_check`), persistencia en PostgreSQL, registro en log corporativo y soporte dual (redirección Blade con flash session + respuesta JSON para integraciones asíncronas).
  - `getCatalogCategories()`: Estructuración oficial de las 6 categorías técnicas (A a la F) y sus servicios asociados con aplicabilidad, unidad de cotización y nivel de complejidad.
  - `getSectors()`: 10 sectores estratégicos con iconografía y descripciones operativas.

#### C. Vistas y Componentes Blade
- **Nueva Vista Principal STI:** `resources/views/tech_services/index.blade.php`
  - Hero especializado con eslogan oficial: *«Credibilidad y garantía es nuestra meta»*.
  - Sección corporativa Quiénes Somos / Misión / Visión y principios operativos.
  - Catálogo interactivo con filtrado dinámico por categorías (A-F) y botón *"Solicitar Presupuesto"* con preselección automática del servicio en el formulario.
  - Sección de 10 sectores atendidos (Hoteles, Industrias, Inmuebles, Hospitales, etc.).
  - Formulario completo de cotización técnica con validación visual y accesibilidad.
  - Bloque de contactos comerciales y coordinación de visitas técnicas.
- **Nuevo Parcial:** `resources/views/partials/landing/business_areas.blade.php`
  - Bloques de acceso equilibrado a ambas divisiones incorporados en la página de inicio.
- **Parciales Actualizados (`resources/views/partials/landing/`):**
  - `navbar.blade.php`: Menú desplegable *"Áreas de Negocio"*, navegación móvil adaptada y CTAs sensibles al contexto de navegación.
  - `hero.blade.php`: Título corporativo H1 dual y accesos directos a ambas divisiones.
  - `about.blade.php`: Misión y visión equilibradas para reflejar ambas líneas de negocio.
  - `footer.blade.php`: 5 columnas con enlaces dedicados a logística y servicios tecnológicos.
- **Layout Base:** `resources/views/layouts/public.blade.php`
  - Metadatos Open Graph, Twitter cards y descripciones actualizadas a *"BLANKISOL SURL"*.

---

### 3. CUMPLIMIENTO DE CRITERIOS DE NEGOCIO Y RESTRICCIONES

| Criterio / Restricción | Estado | Detalle de Implementación |
| :--- | :---: | :--- |
| **No publicación de precios fijos** |  Cumplido | El catálogo opera bajo el modelo *"Solicitar Presupuesto"*, justificando la cotización a medida según metraje, complejidad y accesibilidad. |
| **No afirmaciones sanitarias absolutas ni riesgos normativos** |  Cumplido | Se eliminaron claims desmedidos (erradicación vírica absoluta, prevención infalible de incendios) empleando terminología técnica profesional de saneamiento y desengrase. |
| **No especificaciones técnicas no verificadas** |  Cumplido | No se anuncian presiones extremas de equipos ni temperaturas de vapor no validadas con el inventario físico actual. |
| **Separación estricta de formularios** |  Cumplido | Las cotizaciones de carga van a `contact_requests` vía `/contacto`; las cotizaciones técnicas van a `tech_service_requests` vía `/servicios-tecnologicos/solicitar`. |
| **Trazabilidad y tracking operacional preservados** |  Cumplido | La torre de control de rastreo (`/`, `/tracking/search`) permanece 100% operativa sin alteraciones funcionales. |
| **Procesamiento y persistencia real de datos** |  Cumplido | No existen envíos simulados; cada solicitud se valida y almacena en base de datos. |

---

### 4. RESULTADOS DE PRUEBAS AUTOMATIZADAS

Se ejecutaron las pruebas con PHPUnit sobre PostgreSQL sin regresiones:

```bash
# 1. Pruebas de la nueva división Servicios Tecnológicos Integrales
php artisan test --filter=TechServiceRequestTest
✓ can view tech services landing page (200 OK)
✓ can submit tech service quote request successfully (DB persist + flash session)
✓ validates required fields for tech service quote (Validation errors)
✓ can submit quote request via json (API compatibility)
✓ honeypot field blocks database storage from bots (Anti-spam protection)
Tests: 5 passed (20 assertions)

# 2. Pruebas de regresión en Formulario de Contacto Logístico
php artisan test --filter=ContactFormTest
✓ can submit contact quote request successfully
✓ contact form validates required fields
✓ contact form accepts json requests
Tests: 3 passed (11 assertions)

# 3. Pruebas de regresión en Portal de Tracking
php artisan test --filter=TrackingPortalTest
✓ tracking search page loads
✓ can search equipment by pin and redirect
✓ tracking api endpoint returns json resource
Tests: 3 passed (13 assertions)

# 4. Pruebas de Páginas de Error Personalizadas
php artisan test --filter=CustomErrorPagesTest
✓ non existent route returns custom 404 page
Tests: 1 passed (4 assertions)
```

**Total ejecutado en fase de validación:** 12 pruebas / 48 aserciones — **100% Satisfactorias**.

---

### 5. ACCIONES PENDIENTES / CONFIGURACIÓN DE PRODUCCIÓN

1. **Servidor SMTP para Notificaciones Automáticas:**
   - Actualmente, las solicitudes se guardan en base de datos y se registran en `storage/logs/laravel.log`.
   - Para despachar un correo automático a la gerencia comercial con cada solicitud, configurar en `.env`:
     ```env
     MAIL_MAILER=smtp
     MAIL_HOST=smtp.tudominio.com
     MAIL_PORT=587
     MAIL_USERNAME=notificaciones@blankisol.com
     MAIL_PASSWORD=********
     MAIL_ENCRYPTION=tls
     MAIL_FROM_ADDRESS=notificaciones@blankisol.com
     MAIL_FROM_NAME="BLANKISOL Notificaciones"
     ```
2. **Validación de Enlaces Telefónicos de Atención Comercial:**
   - Los números de Maikel Yunior León Alvarez (+53 56142721) y Yanara Lara Pérez (+53 63512810) figuran como texto informativo con nota de confirmación previa antes de convertirlos en botones de llamada directa `tel:` o enlaces a WhatsApp.
3. **Despliegue a Producción:**
   - Ejecutar en el servidor:
     ```bash
     php artisan migrate --force
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     ```
