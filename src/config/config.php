<?php
// ============================================================
// Remedy Investment — Application Configuration
// ============================================================

define('APP_NAME',    'Remedy Investment');
define('APP_URL',     'http://localhost/Remedy/public');  // Change for production
define('APP_VERSION', '1.0.0');

// ─── Database ───────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'remedy_db');
define('DB_USER', 'root');       // Change for production
define('DB_PASS', '');           // Change for production
define('DB_CHARSET', 'utf8mb4');

// ─── Session ────────────────────────────────────────────────
define('SESSION_NAME',     'remedy_sess');
define('SESSION_LIFETIME', 60 * 60 * 24 * 7); // 7 days

// ─── Security ───────────────────────────────────────────────
define('CSRF_TOKEN_KEY', 'csrf_token');
define('BCRYPT_COST',    12);

// ─── Uploads ────────────────────────────────────────────────
define('UPLOAD_DIR',       __DIR__ . '/../../public/uploads/');
define('UPLOAD_MAX_SIZE',  5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGES',   ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_DOCS',     ['image/jpeg', 'image/png', 'image/webp', 'application/pdf']);

// ─── Email (SMTP) ────────────────────────────────────────────
define('MAIL_HOST',       'smtp.mailtrap.io');
define('MAIL_PORT',       587);
define('MAIL_USER',       '');
define('MAIL_PASS',       '');
define('MAIL_FROM',       'noreply@remedyinvestment.com');
define('MAIL_FROM_NAME',  APP_NAME);
define('MAIL_ENCRYPTION', 'tls');

// ─── Crypto ─────────────────────────────────────────────────
// These are overridden by DB settings — these are fallbacks
define('CRYPTO_USDT_TRC20', '');
define('CRYPTO_USDT_ERC20', '');
define('CRYPTO_BTC',        '');

// ─── Environment ─────────────────────────────────────────────
define('APP_DEBUG', true);   // Set false in production
define('APP_ENV',   'local'); // local | production

// ─── Timezone ────────────────────────────────────────────────
date_default_timezone_set('UTC');

// ─── Error Reporting ────────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
