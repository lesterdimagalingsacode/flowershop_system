<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/vendor/autoload.php';

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

echo "<pre>";
echo "REQUEST_URI:  " . $_SERVER['REQUEST_URI']  . "\n";
echo "SCRIPT_NAME:  " . $_SERVER['SCRIPT_NAME']  . "\n";
echo "SCRIPT_FILE:  " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "dirname:      " . dirname($_SERVER['SCRIPT_NAME']) . "\n";
echo "</pre>";