<?php
// ─────────────────────────────────────────────
//  config.php — Environment loader & constants
// ─────────────────────────────────────────────

declare(strict_types=1);

// ── Load .env file ───────────────────────────
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        throw new RuntimeException(".env file not found at: $path");
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
            putenv("$key=$value");
        }
    }
}

loadEnv(__DIR__ . '/.env');

// ── Helper: read env with fallback ───────────
function env(string $key, mixed $default = null): mixed {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ── App ───────────────────────────────────────
define('APP_NAME',    env('APP_NAME',  '404: Flower Not Found'));
define('APP_ENV',     env('APP_ENV',   'development'));
define('APP_DEBUG',   env('APP_DEBUG', 'true') === 'true');
define('APP_URL', 'http://localhost/flowershop_system');
//define('APP_URL', 'https://relish-pleading-managing.ngrok-free.dev/flowershop_system');


// ── Paths ─────────────────────────────────────
define('APP_PATH',    BASE_PATH . '/app');
define('CORE_PATH',   BASE_PATH . '/core');
define('VIEW_PATH',   APP_PATH  . '/views');
define('STORAGE_PATH',BASE_PATH . '/storage');
define('LOG_PATH',    STORAGE_PATH . '/logs/app.log');

// ── Database ──────────────────────────────────
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'flowershop'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

// ── Session ───────────────────────────────────
define('SESSION_NAME',     env('SESSION_NAME',     'flowershop_session'));
define('SESSION_LIFETIME', (int) env('SESSION_LIFETIME', 7200));

// ── CSRF ──────────────────────────────────────
define('CSRF_TOKEN_NAME', env('CSRF_TOKEN_NAME', 'csrf_token'));

// ── Rate Limiting ─────────────────────────────
define('RATE_LIMIT_MAX',   (int) env('RATE_LIMIT_MAX_ATTEMPTS',   5));
define('RATE_LIMIT_DECAY', (int) env('RATE_LIMIT_DECAY_MINUTES', 15));

// ── PayMongo ──────────────────────────────────
define('PAYMONGO_PUBLIC_KEY',      env('PAYMONGO_PUBLIC_KEY'));
define('PAYMONGO_SECRET_KEY',      env('PAYMONGO_SECRET_KEY'));
define('PAYMONGO_WEBHOOK_SECRET',  env('PAYMONGO_WEBHOOK_SECRET'));
define('PAYMONGO_BASE_URL',        'https://api.paymongo.com/v1');

// ── Mailer ────────────────────────────────────
define('MAIL_HOST',         env('MAIL_HOST',         'smtp.gmail.com'));
define('MAIL_PORT',         (int) env('MAIL_PORT',   587));
define('MAIL_USERNAME',     env('MAIL_USERNAME'));
define('MAIL_PASSWORD',     env('MAIL_PASSWORD'));
define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS'));
define('MAIL_FROM_NAME',    env('MAIL_FROM_NAME',    APP_NAME));

// ── Pagination ────────────────────────────────
define('ITEMS_PER_PAGE', 10);

// ── Roles ─────────────────────────────────────
define('ROLE_ADMIN',    'admin');
define('ROLE_STAFF',    'staff');
define('ROLE_CUSTOMER', 'customer');

// ── Order Statuses ────────────────────────────
define('ORDER_PENDING',    'pending');
define('ORDER_CONFIRMED',  'confirmed');
define('ORDER_PROCESSING', 'processing');
define('ORDER_READY',      'ready');
define('ORDER_DELIVERED',  'delivered');
define('ORDER_CANCELLED',  'cancelled');

// ── Payment Statuses ──────────────────────────
define('PAYMENT_PENDING',   'pending');
define('PAYMENT_PAID',      'paid');
define('PAYMENT_FAILED',    'failed');
define('PAYMENT_REFUNDED',  'refunded');


// npx @tailwindcss/cli -i ./public/css/input.css -o ./public/css/app.css --watch