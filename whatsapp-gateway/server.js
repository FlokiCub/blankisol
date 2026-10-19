require('dotenv').config({ path: __dirname + '/.env' });

const express = require('express');
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const qrcodeTerminal = require('qrcode-terminal');
const QRCode = require('qrcode');
const pino = require('pino');
const path = require('path');
const fs = require('fs');

const app = express();
app.use(express.json());

const PORT = process.env.PORT || 3333;
const API_KEY = process.env.WHATSAPP_LOCAL_KEY || 'scgi_local_key_2026';
// URL base pública del gateway (para generar enlaces QR correctos en producción)
// En local: http://localhost:3333
// En producción: https://tu-dominio.com:3333 o https://api.tu-dominio.com/whatsapp
const PUBLIC_BASE_URL = process.env.PUBLIC_BASE_URL || `http://localhost:${PORT}`;
const AUTH_DIR = path.join(__dirname, 'auth_info_baileys');

let sock = null;
let currentQR = null;
let isConnected = false;
let connectedUser = null;
let qrDataUrl = null;

// Logger silencioso para Baileys
const logger = pino({ level: 'silent' });

async function startWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
    const { version } = await fetchLatestBaileysVersion();

    sock = makeWASocket({
        version,
        logger,
        printQRInTerminal: false,
        auth: state,
        browser: ['SCGI Torre de Control', 'Chrome', '120.0.0']
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            currentQR = qr;
            isConnected = false;
            try {
                qrDataUrl = await QRCode.toDataURL(qr);
            } catch (err) {
                console.error('Error generando QR data URL:', err);
            }

            console.log('\n╔═══════════════════════════════════════════════════════════════╗');
            console.log('║ 📲 ESCANEA ESTE CÓDIGO QR CON WHATSAPP EN TU TELÉFONO        ║');
            console.log('║ (WhatsApp > Ajustes > Dispositivos vinculados > Vincular)     ║');
            console.log('╚═══════════════════════════════════════════════════════════════╝\n');
            qrcodeTerminal.generate(qr, { small: true });
            console.log('\n🌐 O abre en tu navegador: ' + PUBLIC_BASE_URL + '/qr\n');
        }

        if (connection === 'close') {
            isConnected = false;
            connectedUser = null;
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

            console.log(`⚠️ Conexión cerrada (Código: ${statusCode}). Reintentando: ${shouldReconnect}`);

            if (shouldReconnect) {
                setTimeout(startWhatsApp, 3000);
            } else {
                console.log('❌ Sesión cerrada por el usuario. Limpiando credenciales para nuevo QR...');
                if (fs.existsSync(AUTH_DIR)) {
                    fs.rmSync(AUTH_DIR, { recursive: true, force: true });
                }
                setTimeout(startWhatsApp, 2000);
            }
        } else if (connection === 'open') {
            isConnected = true;
            currentQR = null;
            qrDataUrl = null;
            connectedUser = sock.user?.id || 'Conectado';
            console.log('\n🎉 ¡CONECTADO CON ÉXITO A WHATSAPP!');
            console.log(`📱 Usuario vinculado: ${connectedUser}`);
            console.log(`🚀 Gateway listo para enviar mensajes en ${PUBLIC_BASE_URL}/send\n`);
        }
    });
}

// Iniciar socket
startWhatsApp();

// 1. Estado actual del Gateway
app.get('/status', (req, res) => {
    res.json({
        connected: isConnected,
        user: connectedUser,
        has_qr: !!currentQR
    });
});

// 2. Interfaz visual web para ver el código QR
app.get('/qr', (req, res) => {
    res.send(`
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vincular WhatsApp — SCGI Gateway</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
    .card { background: #1e293b; border: 1px solid #334155; border-radius: 24px; padding: 36px; max-width: 440px; width: 100%; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
    h1 { font-size: 20px; font-weight: 800; margin-bottom: 8px; color: #fff; }
    p { font-size: 13px; color: #94a3b8; line-height: 1.5; margin-top: 0; }
    .qr-box { background: white; padding: 16px; border-radius: 16px; display: inline-block; margin: 20px 0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); }
    .qr-box img { width: 260px; height: 260px; display: block; }
    .badge { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; margin-bottom: 16px; }
    .badge-wait { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .badge-success { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .steps { text-align: left; background: #0f172a; padding: 16px; border-radius: 12px; font-size: 12px; color: #cbd5e1; margin-top: 20px; border: 1px solid #334155; }
    .steps ol { margin: 0; padding-left: 20px; }
    .steps li { margin-bottom: 6px; }
  </style>
</head>
<body>
  <div class="card">
    ${isConnected ? `
      <div class="badge badge-success">● DISPOSITIVO VINCULADO Y LISTO</div>
      <h1>¡Gateway Conectado!</h1>
      <p>Tu WhatsApp está activo y listo para despachar notificaciones automáticas desde el sistema SCGI.</p>
      <div style="font-size: 48px; margin: 24px 0;">🎉</div>
      <p style="font-family: monospace; font-size: 12px; color: #38bdf8;">${connectedUser || ''}</p>
    ` : (qrDataUrl ? `
      <div class="badge badge-wait">● ESPERANDO ESCANEO</div>
      <h1>Escanea con WhatsApp</h1>
      <p>Abre WhatsApp en tu teléfono y escanea este código para vincular el sistema:</p>
      <div class="qr-box">
        <img src="${qrDataUrl}" alt="Código QR WhatsApp" />
      </div>
      <div class="steps">
        <ol>
          <li>Abre <strong>WhatsApp</strong> en tu teléfono.</li>
          <li>Toca <strong>Ajustes</strong> o <strong>Menú (⋮)</strong>.</li>
          <li>Selecciona <strong>Dispositivos vinculados</strong>.</li>
          <li>Toca <strong>Vincular un dispositivo</strong> y apunta la cámara a este código.</li>
        </ol>
      </div>
    ` : `
      <div class="badge badge-wait">● INICIALIZANDO</div>
      <h1>Generando Código QR...</h1>
      <p>Espera un segundo mientras se inicia el servicio de WhatsApp.</p>
    `)}
  </div>
  <script>
    // Auto-recargar cada 4 segundos si no está conectado
    ${!isConnected ? `
      setInterval(() => {
        fetch('/status')
          .then(r => r.json())
          .then(data => {
            if (data.connected || data.has_qr) {
              window.location.reload();
            }
          })
          .catch(() => {});
      }, 4000);
    ` : ''}
  </script>
</body>
</html>
    `);
});

// 3. Endpoint para envío de mensajes
app.post('/send', async (req, res) => {
    const { phone, message, api_key } = req.body;

    if (api_key && api_key !== API_KEY) {
        return res.status(401).json({ success: false, error: 'API Key inválida' });
    }

    if (!isConnected || !sock) {
        return res.status(503).json({
            success: false,
            error: 'El Gateway no está conectado a WhatsApp. Abre ' + PUBLIC_BASE_URL + '/qr para escanear el código QR.'
        });
    }

    if (!phone || !message) {
        return res.status(400).json({ success: false, error: 'Faltan parámetros phone o message' });
    }

    try {
        // Limpiar teléfono y asegurar sufijo de WhatsApp
        let cleanDigits = phone.replace(/\\D/g, '');
        if (cleanDigits.length === 8 && cleanDigits.startsWith('5')) {
            cleanDigits = '53' + cleanDigits;
        }

        const jid = `${cleanDigits}@s.whatsapp.net`;

        const sentMsg = await sock.sendMessage(jid, { text: message });

        res.json({
            success: true,
            message_id: sentMsg?.key?.id || ('BAILEYS-' + Date.now()),
            recipient: cleanDigits,
            timestamp: new Date().toISOString()
        });
    } catch (err) {
        console.error('Error enviando mensaje WhatsApp:', err);
        res.status(500).json({
            success: false,
            error: err.message || 'Error desconocido al enviar mensaje'
        });
    }
});

app.listen(PORT, () => {
    console.log(`\n🚀 [WhatsApp Gateway SCGI] Escuchando en http://localhost:${PORT}`);
    console.log(`👉 Abre en tu navegador para ver el QR: ${PUBLIC_BASE_URL}/qr\n`);
});
