# Guía de Despliegue WhatsApp Gateway - freehosting.dev

## Problema Original
El gateway WhatsApp usaba `http://127.0.0.1:3333` hardcodeado, lo que funciona en local pero falla en producción porque:
1. El hosting no permite acceso a `localhost` desde la aplicación Laravel
2. El gateway debe ser accesible via URL pública

## Solución Implementada

### 1. Variables de Entorno Configurables

**Laravel (`.env`):**
```env
WHATSAPP_PROVIDER=local
WHATSAPP_LOCAL_URL=https://blankysol.freehosting.dev:3333  # URL PÚBLICA del gateway
WHATSAPP_LOCAL_KEY=scgi_local_key_2026
```

**Gateway WhatsApp (`whatsapp-gateway/.env`):**
```env
PORT=3333
WHATSAPP_LOCAL_KEY=scgi_local_key_2026
PUBLIC_BASE_URL=https://blankysol.freehosting.dev:3333  # URL PÚBLICA para mostrar QR
```

### 2. Opciones de Despliegue en freehosting.dev

#### Opción A: Gateway en el MISMO servidor (Recomendado si el hosting lo permite)
Si freehosting.dev permite procesos Node.js persistentes y puertos personalizados:

1. Sube la carpeta `whatsapp-gateway/` al hosting
2. Instala dependencias: `npm install`
3. Configura `whatsapp-gateway/.env` con `PUBLIC_BASE_URL=https://blankysol.freehosting.dev:3333`
4. Inicia el gateway: `npm start` (o usa PM2: `pm2 start server.js --name whatsapp-gateway`)
5. En Laravel `.env`: `WHATSAPP_LOCAL_URL=https://blankysol.freehosting.dev:3333`

#### Opción B: Gateway en SERVIDOR SEPARADO (VPS/Cloud)
Si freehosting.dev NO permite Node.js persistente:

1. Despliega el gateway en un VPS (DigitalOcean, AWS, Hetzner, etc.)
2. Configura dominio/subdominio: `wa-api.tudominio.com`
3. Usa nginx/Apache como proxy reverso con SSL
4. En Laravel `.env`: `WHATSAPP_LOCAL_URL=https://wa-api.tudominio.com`

#### Opción C: Meta Cloud API (Alternativa sin gateway propio)
Si no puedes mantener un gateway propio:

1. Crea una app en [Meta Developers](https://developers.facebook.com/)
2. Configura WhatsApp Business API
3. En Laravel `.env`:
```env
WHATSAPP_PROVIDER=meta
WHATSAPP_PHONE_NUMBER_ID=tu_phone_number_id
WHATSAPP_ACCESS_TOKEN=tu_access_token
WHATSAPP_BUSINESS_ACCOUNT_ID=tu_business_account_id
WHATSAPP_APP_SECRET=tu_app_secret
```

### 3. Configuración de Nginx (Proxy Reverso con SSL)

```nginx
server {
    listen 443 ssl http2;
    server_name wa-api.tudominio.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    location / {
        proxy_pass http://localhost:3333;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
}
```

### 4. Verificación

1. Accede a `https://blankysol.freehosting.dev:3333/qr` (o tu URL configurada)
2. Debería mostrar el código QR para escanear con WhatsApp
3. En Laravel, las notificaciones deberían enviarse correctamente

### 5. Variables Importantes

| Variable | Local | Producción | Descripción |
|----------|-------|------------|-------------|
| `WHATSAPP_LOCAL_URL` | `http://127.0.0.1:3333` | `https://tu-dominio.com:3333` | URL donde Laravel llama al gateway |
| `PUBLIC_BASE_URL` | `http://localhost:3333` | `https://tu-dominio.com:3333` | URL que el gateway muestra para el QR |
| `WHATSAPP_LOCAL_KEY` | `scgi_local_key_2026` | `clave_segura_produccion` | Clave API compartida (cambiar en prod) |

### 6. Seguridad en Producción

1. **Cambia la API Key:** Genera una clave segura: `openssl rand -hex 32`
2. **Usa HTTPS:** Obligatorio en producción
3. **Restringe IPs:** Configura firewall para solo permitir acceso desde tu servidor Laravel
4. **Rate Limiting:** Implementa límite de requests en nginx

### 7. Comandos Útiles

```bash
# Instalar dependencias
cd whatsapp-gateway && npm install

# Desarrollo local
npm run dev  # o: node server.js

# Producción con PM2
pm2 start server.js --name whatsapp-gateway
pm2 save
pm2 startup

# Ver logs
pm2 logs whatsapp-gateway

# Reiniciar
pm2 restart whatsapp-gateway
```

## Archivos Modificados

- `config/services.php` - Defaults y comentarios actualizados
- `.env.example` - Variables de ejemplo completas
- `.env` - Configuración local actualizada
- `whatsapp-gateway/server.js` - Usa `PUBLIC_BASE_URL` configurable
- `whatsapp-gateway/.env` - Nuevo archivo de configuración del gateway
- `whatsapp-gateway/package.json` - Agregada dependencia `dotenv`