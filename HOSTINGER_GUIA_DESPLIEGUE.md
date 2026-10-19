# 🚀 Guía de Despliegue en Hostinger — SCGI BLANKISOL S.R.L.

Este proyecto ha sido optimizado y empaquetado para subirse de manera rápida a **Hostinger** (hPanel compartido, Cloud Hosting o VPS).

El archivo listo para subir es:
📁 **`deploy_hostinger_blankisol.zip`** (Ubicado en la raíz de tu proyecto: `d:\XAMPP\htdocs\mi-web\deploy_hostinger_blankisol.zip`, tamaño: **~39 MB**).

---

## 📦 Contenido del Archivo `.zip`
- ✅ **Assets Vite ya compilados para producción** en `public/build/` (CSS minificado con Tailwind y JS bundles sin requerir Node.js en Hostinger).
- ✅ **Carpeta `vendor/` completa con Autoloader optimizado** (`classmap` de 7,200+ clases listo para máxima velocidad sin requerir ejecutar Composer en el servidor).
- ✅ **`.htaccess` de seguridad en la raíz** que redirige a `public/` y bloquea automáticamente cualquier intento de acceso web a `.env`, `storage/`, `vendor/` o `bootstrap/`.
- ✅ **Cachés locales limpias** (sin rutas ni configuraciones fijas de XAMPP local).
- ❌ **Excluidos:** `node_modules` (ahorra cientos de MB), historial `.git`, suites de tests y logs locales.

---

## 🛠️ Pasos para Subir y Activar en Hostinger (hPanel)

### Paso 1: Subir y Extraer el Archivo `.zip`
1. Ingresa a tu panel de **Hostinger (hPanel)**.
2. Ve a **Sitios web** ➔ Selecciona tu dominio ➔ **Administrador de Archivos (File Manager)**.
3. Entra a la carpeta `public_html/`.
4. Haz clic en el botón **Subir (Upload)** ➔ Selecciona **`deploy_hostinger_blankisol.zip`**.
5. Una vez subido, haz clic derecho sobre el archivo `.zip` y selecciona **Extraer (Extract)** en el directorio actual (`public_html/`).

---

### Paso 2: Configurar el archivo `.env`
1. Dentro del Administrador de Archivos, busca el archivo **`.env.production.example`**.
2. Renómbralo a **`.env`** (si ya existía un `.env`, puedes editarlo o reemplazarlo).
3. Edita los siguientes valores con los datos de tu hosting:
   ```env
   APP_NAME="SCGI BLANKISOL"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://tudominio.com

   # Configuración de tu base de datos de Hostinger
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u123456789_scgi
   DB_USERNAME=u123456789_usuario
   DB_PASSWORD=TuPasswordSeguro123!
   ```

---

### Paso 3: Crear la Base de Datos en Hostinger
1. En hPanel, ve a **Bases de datos** ➔ **Administración de bases de datos MySQL**.
2. Crea una base de datos nueva (ej: `u123456789_scgi`), un usuario y una contraseña segura.
3. Coloca exactamente esos nombres en tu archivo `.env`.

---

### Paso 4: Ejecutar las Migraciones (2 Opciones)

#### Opción A (Recomendada si usas SSH / Terminal de Hostinger):
1. En hPanel, activa el acceso **SSH** en **Avanzado ➔ Acceso SSH**.
2. Abre la terminal o conéctate por SSH y navega a tu proyecto:
   ```bash
   cd public_html
   php artisan migrate --force --seed
   php artisan storage:link
   php artisan optimize
   ```

#### Opción B (Si no usas terminal / Solo phpMyAdmin):
1. Puedes importar las tablas directamente desde **phpMyAdmin** de Hostinger.
2. Si prefieres ejecutarlo mediante web, puedes acceder temporalmente a una ruta de setup o ejecutar `php artisan migrate` desde el programador de tareas (Cron Job) de Hostinger con el comando:
   ```bash
   /usr/bin/php /home/uXXXXXXX/domains/tudominio.com/public_html/artisan migrate --force --seed
   ```

---

### Paso 5: Permisos de Carpetas (Importante)
Verifica que las siguientes carpetas tengan permisos de escritura (`755` o `775`):
- `storage/` (y todas sus subcarpetas: `storage/framework/`, `storage/logs/`, `storage/app/`)
- `bootstrap/cache/`

---

### Paso 6: Configuración del Document Root (Directorio Raíz)
Tienes dos métodos compatibles:
- **Método 1 (El más limpio):** En Hostinger hPanel ➔ **Sitios web ➔ General ➔ Configuración ➔ Document Root**, cámbialo a `public_html/public`.
- **Método 2 (Automático):** Déjalo como `public_html`. El archivo `.htaccess` incluido en la raíz redirigirá internamente todas las visitas a `public/` de forma segura.

---

### ✅ ¡Listo!
Tu sistema SCGI BLANKISOL con el módulo de Rentabilidad de Paquetería, control de accesos RBAC y reportes ejecutivos estará 100% operativo en Hostinger.
