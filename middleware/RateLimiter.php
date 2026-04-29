<?php
// ─────────────────────────────────────────────
//  middleware/RateLimiter.php
//  Limits login attempts per IP address.
//  Max attempts stored in PHP session.
//  Blocks for RATE_LIMIT_DECAY minutes.
// ─────────────────────────────────────────────

declare(strict_types=1);

class RateLimiter {

    public function handle(string $role = 'throttle', array $params = []): void {
        $key = $this->getKey();

        if ($this->isTooManyAttempts($key)) {
            $seconds = $this->availableIn($key);
            $minutes = ceil($seconds / 60);

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                http_response_code(429);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => "Too many attempts. Please try again in {$minutes} minute(s).",
                    'retry_after' => $seconds,
                ]);
                exit;
            }

            Session::flash('message', "Too many login attempts. Please try again in {$minutes} minute(s).", 'error');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    // ── Record a failed attempt ───────────────
    public static function hit(string $action = 'login'): void {
        $key  = self::buildKey($action);
        $data = self::getData($key);

        $data['attempts'] = ($data['attempts'] ?? 0) + 1;
        $data['first_attempt'] ??= time();

        Session::set($key, $data);
    }

    // ── Clear on successful login ─────────────
    public static function clear(string $action = 'login'): void {
        $key = self::buildKey($action);
        Session::delete($key);
    }

    // ── Check if too many attempts ────────────
    public static function isTooManyAttempts(string $key): bool {
        $data = self::getData($key);
        if (empty($data)) return false;

        $decaySeconds = RATE_LIMIT_DECAY * 60;
        $elapsed      = time() - ($data['first_attempt'] ?? time());

        // Reset if decay window has passed
        if ($elapsed > $decaySeconds) {
            Session::delete($key);
            return false;
        }

        return ($data['attempts'] ?? 0) >= RATE_LIMIT_MAX;
    }

    // ── Seconds until unlock ──────────────────
    public static function availableIn(string $key): int {
        $data = self::getData($key);
        if (empty($data)) return 0;

        $decaySeconds = RATE_LIMIT_DECAY * 60;
        $elapsed      = time() - ($data['first_attempt'] ?? time());
        return max(0, $decaySeconds - $elapsed);
    }

    // ── Remaining attempts ────────────────────
    public static function remainingAttempts(string $action = 'login'): int {
        $key  = self::buildKey($action);
        $data = self::getData($key);
        return max(0, RATE_LIMIT_MAX - ($data['attempts'] ?? 0));
    }

    // ── Private helpers ───────────────────────
    private function getKey(): string {
        return self::buildKey('login');
    }

    private static function buildKey(string $action): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return "rate_limit_{$action}_{$ip}";
    }

    private static function getData(string $key): array {
        return Session::get($key, []);
    }
}
