<?php
// ─────────────────────────────────────────────
//  core/ErrorHandler.php — Global Error Handler
// ─────────────────────────────────────────────

declare(strict_types=1);

class ErrorHandler {

    public static function register(): void {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);

        ini_set('display_errors', APP_DEBUG ? '1' : '0');
        ini_set('log_errors', '1');
        ini_set('error_log', LOG_PATH);
        error_reporting(E_ALL);
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        if (!(error_reporting() & $errno)) return false;
        Logger::error("PHP Error [$errno]: $errstr in $errfile on line $errline");
        if (APP_DEBUG) throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        return true;
    }

    public static function handleException(Throwable $e): void {
        Logger::error(sprintf(
            "Uncaught %s: %s in %s:%d\n%s",
            get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()
        ));

        if (self::isJsonRequest()) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => APP_DEBUG ? $e->getMessage() : 'Server error.']);
            exit;
        }

        http_response_code(500);
        if (APP_DEBUG) {
            self::renderDebugPage($e);
        } else {
            $f = VIEW_PATH . '/errors/500.php';
            if (file_exists($f)) require $f;
            else echo '<h1>500 — Server Error</h1>';
        }
        exit;
    }

    public static function handleShutdown(): void {
        $e = error_get_last();
        if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::handleException(new ErrorException($e['message'], 0, $e['type'], $e['file'], $e['line']));
        }
    }

    private static function renderDebugPage(Throwable $e): void {
        $class = get_class($e);
        $msg   = htmlspecialchars($e->getMessage(), ENT_QUOTES);
        $file  = htmlspecialchars($e->getFile(), ENT_QUOTES);
        $trace = htmlspecialchars($e->getTraceAsString(), ENT_QUOTES);
        echo <<<HTML
        <!DOCTYPE html><html>
        <head><title>Error</title><script src="https://cdn.tailwindcss.com"></script></head>
        <body class="bg-gray-950 text-gray-100 p-8 font-mono text-sm">
        <div class="max-w-4xl mx-auto">
          <div class="bg-red-900/40 border border-red-500 rounded-xl p-6 mb-6">
            <p class="text-red-400 text-xs mb-1 uppercase">{$class}</p>
            <h1 class="text-white text-xl font-bold mb-2">{$msg}</h1>
            <p class="text-gray-400">{$file} line {$e->getLine()}</p>
          </div>
          <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
            <pre class="text-gray-300 text-xs whitespace-pre-wrap">{$trace}</pre>
          </div>
        </div></body></html>
        HTML;
    }

    private static function isJsonRequest(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}