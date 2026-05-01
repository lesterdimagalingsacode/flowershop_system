<?php
// ─────────────────────────────────────────────
//  middleware/RateLimiter.php
//  Limits login attempts per IP + email.
//  Attempts stored in DB — cannot be bypassed
//  by clearing cookies or session.
//  Blocks for RATE_LIMIT_DECAY minutes.
// ─────────────────────────────────────────────

declare(strict_types=1);

class RateLimiter {

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Run the throttle check ────────────────
    public function handle(string $role = 'throttle', array $params = []): void {
        $ip    = $this->getIp();
        $email = trim(strtolower($_POST['email'] ?? ''));

        if ($this->isTooManyAttempts($ip, $email)) {
            $seconds = $this->availableIn($ip, $email);
            $minutes = (int) ceil($seconds / 60);

            // JSON response for AJAX
            if (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            ) {
                http_response_code(429);
                header('Content-Type: application/json');
                echo json_encode([
                    'success'     => false,
                    'message'     => "Too many attempts. Please try again in {$minutes} minute(s).",
                    'retry_after' => $seconds,
                ]);
                exit;
            }

            // Normal redirect
            Session::flash(
                'message',
                "Too many login attempts. Please try again in {$minutes} minute(s).",
                'error'
            );
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    // ── Record a failed attempt in DB ─────────
    public static function hit(string $action = 'login'): void {
        $db    = Database::getInstance();
        $ip    = self::getIpStatic();
        $email = trim(strtolower($_POST['email'] ?? ''));

        $db->execute(
            "INSERT INTO login_attempts (ip_address, email, attempted_at)
             VALUES (?, ?, NOW())",
            [$ip, $email]
        );
    }

    // ── Clear attempts on successful login ────
    public static function clear(string $action = 'login'): void {
        $db    = Database::getInstance();
        $ip    = self::getIpStatic();
        $email = trim(strtolower($_POST['email'] ?? ''));

        $db->execute(
            "DELETE FROM login_attempts
             WHERE ip_address = ? OR email = ?",
            [$ip, $email]
        );
    }

    // ── Check if blocked ─────────────────────
    public static function isTooManyAttempts(string $ip, string $email = ''): bool {
        $db      = Database::getInstance();
        $decay   = RATE_LIMIT_DECAY; // minutes
        $max     = RATE_LIMIT_MAX;   // max attempts

        $row = $db->queryOne(
            "SELECT COUNT(*) as attempts
             FROM login_attempts
             WHERE (ip_address = ? OR email = ?)
               AND attempted_at >= NOW() - INTERVAL ? MINUTE",
            [$ip, $email, $decay]
        );

        return (int)($row['attempts'] ?? 0) >= $max;
    }

    // ── Seconds until block lifts ─────────────
    public static function availableIn(string $ip, string $email = ''): int {
        $db    = Database::getInstance();
        $decay = RATE_LIMIT_DECAY;

        // Find the oldest attempt in the current window
        $row = $db->queryOne(
            "SELECT MIN(attempted_at) as oldest
             FROM login_attempts
             WHERE (ip_address = ? OR email = ?)
               AND attempted_at >= NOW() - INTERVAL ? MINUTE",
            [$ip, $email, $decay]
        );

        if (empty($row['oldest'])) return 0;

        $oldestTimestamp = strtotime($row['oldest']);
        $unlocksAt       = $oldestTimestamp + ($decay * 60);
        return max(0, $unlocksAt - time());
    }

    // ── Remaining attempts ────────────────────
    public static function remainingAttempts(string $action = 'login'): int {
        $db    = Database::getInstance();
        $ip    = self::getIpStatic();
        $email = trim(strtolower($_POST['email'] ?? ''));
        $decay = RATE_LIMIT_DECAY;

        $row = $db->queryOne(
            "SELECT COUNT(*) as attempts
             FROM login_attempts
             WHERE (ip_address = ? OR email = ?)
               AND attempted_at >= NOW() - INTERVAL ? MINUTE",
            [$ip, $email, $decay]
        );

        return max(0, RATE_LIMIT_MAX - (int)($row['attempts'] ?? 0));
    }

    // ── Auto-clean old attempts ───────────────
    // Call this occasionally to keep the table clean
    public static function purgeOld(): void {
        $db    = Database::getInstance();
        $decay = RATE_LIMIT_DECAY;

        $db->execute(
            "DELETE FROM login_attempts
             WHERE attempted_at < NOW() - INTERVAL ? MINUTE",
            [$decay]
        );
    }

    // ── Helpers ───────────────────────────────
    private function getIp(): string {
        return self::getIpStatic();
    }

    private static function getIpStatic(): string {
        return $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'unknown';
    }
}