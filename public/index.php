<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/vendor/autoload.php';

// ── Autoload our own classes ──────────────────
spl_autoload_register(function (string $class): void {
    $paths = [
        BASE_PATH . '/core/'            . $class . '.php',
        BASE_PATH . '/middleware/'      . $class . '.php',
        BASE_PATH . '/app/models/'      . $class . '.php',
        BASE_PATH . '/app/controllers/' . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── Load global helper functions ──────────────
// Must be AFTER autoloader so Session/classes are available
require_once BASE_PATH . '/core/Session.php';
require_once BASE_PATH . '/core/Logger.php';
require_once BASE_PATH . '/core/Validator.php';
require_once BASE_PATH . '/middleware/CSRFMiddleware.php';
require_once BASE_PATH . '/middleware/RateLimiter.php';
require_once BASE_PATH . '/middleware/AuthMiddleware.php';

// ── Boot and run ──────────────────────────────
$app = new App();
$app->run();