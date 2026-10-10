<?php
// ============================================================
// Remedy Investment — Application Configuration
// ============================================================

define('APP_NAME',    getenv('APP_NAME') ?: 'Remedy Investment');
define('APP_VERSION', '1.0.0');

// ─── Dynamic APP_URL Resolution ──────────────────────────────
$railwayDomain = getenv('RAILWAY_PUBLIC_DOMAIN') ?: getenv('RAILWAY_STATIC_URL');
if (!empty($railwayDomain)) {
    $defaultAppUrl = 'https://' . rtrim($railwayDomain, '/');
} elseif (!empty($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
    $defaultAppUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];
} else {
    $defaultAppUrl = 'http://localhost:8000';
}
define('APP_URL', getenv('APP_URL') ?: $defaultAppUrl);

// ─── Database Configuration (Auto-detecting Railway & MySQL) ──
$dbHost = 'localhost';
$dbPort = '3306';
$dbName = 'remedy_db';
$dbUser = 'root';
$dbPass = '';

// 1. Check for combined URL (Railway MYSQL_URL or DATABASE_URL)
$dbUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL');
if ($dbUrl) {
    $parsed = parse_url($dbUrl);
    if ($parsed) {
        $dbHost = $parsed['host'] ?? $dbHost;
        $dbPort = (string)($parsed['port'] ?? $dbPort);
        $dbUser = $parsed['user'] ?? $dbUser;
        $dbPass = $parsed['pass'] ?? $dbPass;
        $dbName = isset($parsed['path']) ? ltrim($parsed['path'], '/') : $dbName;
    }
} else {
    // 2. Check individual Railway / Docker environment variables
    $dbHost = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: $dbHost;
    $dbPort = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: $dbPort;
    $dbName = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: $dbName;
    $dbUser = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: getenv('DB_USER') ?: $dbUser;
    $dbPass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS') !== false ? (getenv('DB_PASS') ?: '') : $dbPass;
}

define('DB_HOST',    $dbHost);
define('DB_PORT',    $dbPort);
define('DB_NAME',    $dbName);
define('DB_USER',    $dbUser);
define('DB_PASS',    $dbPass);
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
define('MAIL_HOST',       getenv('MAIL_HOST') ?: 'smtp.mailtrap.io');
define('MAIL_PORT',       (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USER',       getenv('MAIL_USER') ?: '');
define('MAIL_PASS',       getenv('MAIL_PASS') ?: '');
define('MAIL_FROM',       getenv('MAIL_FROM') ?: 'noreply@remedyinvestment.com');
define('MAIL_FROM_NAME',  APP_NAME);
define('MAIL_ENCRYPTION', getenv('MAIL_ENCRYPTION') ?: 'tls');

// ─── Crypto Fallbacks ────────────────────────────────────────
define('CRYPTO_USDT_TRC20', getenv('CRYPTO_USDT_TRC20') ?: '');
define('CRYPTO_USDT_ERC20', getenv('CRYPTO_USDT_ERC20') ?: '');
define('CRYPTO_BTC',        getenv('CRYPTO_BTC') ?: '');

// ─── Environment ─────────────────────────────────────────────
define('APP_DEBUG', (getenv('APP_DEBUG') === 'false' || getenv('APP_ENV') === 'production') ? false : true);
define('APP_ENV',   getenv('APP_ENV') ?: 'production');

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
