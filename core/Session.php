<?php
// ─────────────────────────────────────────────
//  core/Session.php — Secure Session Management
//  • Secure cookie flags
//  • Session fixation prevention
//  • Flash messages (one-time read)
//  • Auth helpers
// ─────────────────────────────────────────────

declare(strict_types=1);

class Session {

    private static bool $started = false;

    // ── Boot ──────────────────────────────────
    public static function start(): void {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        session_name(SESSION_NAME);

        // ── SameSite cookie strategy ──────────
        // On HTTPS (ngrok, InfinityFree, production):
        //   SameSite=None + Secure=true → session persists after PayMongo redirect
        // On HTTP (plain localhost):
        //   SameSite=Lax + Secure=false → normal behavior, no cross-site redirect
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => $isHttps ? 'None' : 'Lax',
            // 'None'  → allows cookie after cross-site redirect (PayMongo → your site)
            //           requires HTTPS — works on ngrok + InfinityFree
            // 'Lax'   → fallback for plain HTTP localhost
            //           session won't persist after PayMongo redirect on localhost
            //           but works fine for everything else
        ]);

        session_start();
        self::$started = true;

        // Regenerate session ID every 30 minutes (session fixation prevention)
        if (!isset($_SESSION['_last_regenerated'])) {
            session_regenerate_id(true);
            $_SESSION['_last_regenerated'] = time();
        } elseif (time() - $_SESSION['_last_regenerated'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_last_regenerated'] = time();
        }
    }

    // ── Get / Set / Delete ────────────────────
    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void {
        unset($_SESSION[$key]);
    }

    // ── Flash Messages ────────────────────────
    // Flash messages are stored for ONE read then deleted automatically.

    public static function flash(string $key, mixed $message, string $type = 'info'): void {
        $_SESSION['_flash'][$key] = ['message' => $message, 'type' => $type];
    }

    public static function getFlash(string $key): ?array {
        if (!isset($_SESSION['_flash'][$key])) return null;
        $flash = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $flash;
    }

    public static function getAllFlash(): array {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }

    public static function hasFlash(string $key): bool {
        return isset($_SESSION['_flash'][$key]);
    }

    // ── Auth Helpers ──────────────────────────
    public static function login(array $user): void {
        // Regenerate session ID on login (session fixation prevention)
        session_regenerate_id(true);
        $_SESSION['_last_regenerated'] = time();

        self::set('auth_user', [
            'id'         => $user['id'],
            'first_name' => $user['first_name'],
            'middle_name'=> $user['middle_name'] ?? null,
            'last_name'  => $user['last_name'],
            'name'       => $user['name'], // full name from appendFullName()
            'email'      => $user['email'],
            'role'       => $user['role'],
        ]);
    }

    public static function logout(): void {
        self::delete('auth_user');
        session_regenerate_id(true);
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn(): bool {
        return self::has('auth_user');
    }

    public static function user(): ?array {
        return self::get('auth_user');
    }

    public static function userId(): ?int {
        return self::get('auth_user')['id'] ?? null;
    }

    public static function userRole(): ?string {
        return self::get('auth_user')['role'] ?? null;
    }

    public static function isAdmin(): bool {
        return self::userRole() === ROLE_ADMIN;
    }

    public static function isStaff(): bool {
        return in_array(self::userRole(), [ROLE_ADMIN, ROLE_STAFF], true);
    }

    public static function isCustomer(): bool {
        return self::userRole() === ROLE_CUSTOMER;
    }

    // ── Cart Helpers ──────────────────────────
    public static function getCart(): array {
        return self::get('cart', []);
    }

    public static function setCart(array $cart): void {
        self::set('cart', $cart);
    }

    public static function clearCart(): void {
        self::delete('cart');
    }
}