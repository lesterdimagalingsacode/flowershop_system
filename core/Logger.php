<?php
// ─────────────────────────────────────────────
//  core/Logger.php — File + DB Audit Logger
// ─────────────────────────────────────────────

declare(strict_types=1);

class Logger {

    public static function info(string $message, array $context = []): void {
        self::writeLog('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void {
        self::writeLog('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::writeLog('ERROR', $message, $context);
    }

    // ── Audit log to DB (user actions) ────────
    public static function audit(string $action, array $context = []): void {
        self::writeLog('AUDIT', $action, $context);
        try {
            $db     = Database::getInstance();
            $userId = Session::userId();
            $db->execute(
                "INSERT INTO audit_logs (user_id, action, context, ip_address, created_at)
                 VALUES (?, ?, ?, ?, NOW())",
                [
                    $userId,
                    $action,
                    json_encode($context),
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                ]
            );
        } catch (Throwable) {
            // Don't let logging failure break the app
        }
    }

    // ── Write to file ─────────────────────────
    private static function writeLog(string $level, string $message, array $context): void {
        $logDir = dirname(LOG_PATH);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $userId    = Session::isLoggedIn() ? 'user:' . Session::userId() : 'guest';
        $ip        = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ctx       = empty($context) ? '' : ' | ' . json_encode($context);
        $line      = "[$timestamp] [$level] [$userId] [$ip] $message$ctx" . PHP_EOL;

        file_put_contents(LOG_PATH, $line, FILE_APPEND | LOCK_EX);
    }
}
