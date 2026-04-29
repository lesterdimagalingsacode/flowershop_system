<?php
// ─────────────────────────────────────────────
//  middleware/CSRFMiddleware.php
//  Generates and validates CSRF tokens.
//  Every POST/PUT/PATCH/DELETE must include
//  a valid token in the request.
// ─────────────────────────────────────────────

declare(strict_types=1);

class CSRFMiddleware {

    public function handle(string $role = 'csrf', array $params = []): void {
        $method = $_SERVER['REQUEST_METHOD'];

        // Only validate on state-changing requests
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        // Skip CSRF check for PayMongo webhook (uses signature verification instead)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (str_ends_with($uri, '/webhook/paymongo')) {
            return;
        }

        $token = $_POST[CSRF_TOKEN_NAME]
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        if (!$token || !self::verify($token)) {
            // If Fetch/AJAX request → return JSON error
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                http_response_code(419);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'CSRF token mismatch.']);
                exit;
            }

            // Otherwise show error page
            http_response_code(419);
            $file = VIEW_PATH . '/errors/419.php';
            if (file_exists($file)) require $file;
            else echo '<h1>419 — CSRF Token Mismatch</h1><p>Please go back and try again.</p>';
            exit;
        }
    }

    // ── Generate a token (call in every form view) ──
    public static function generate(): string {
        if (!Session::has(CSRF_TOKEN_NAME)) {
            Session::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
        }
        return Session::get(CSRF_TOKEN_NAME);
    }

    // ── Verify a submitted token ──────────────
    public static function verify(string $token): bool {
        $stored = Session::get(CSRF_TOKEN_NAME);
        if (!$stored) return false;
        return hash_equals($stored, $token);
    }

    // ── Regenerate after use ──────────────────
    public static function regenerate(): void {
        Session::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
    }

    // ── HTML hidden input helper ──────────────
    // Use csrf_field() in every form
    public static function field(): string {
        $token = self::generate();
        $name  = CSRF_TOKEN_NAME;
        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$token}\">";
    }

    // ── Meta tag helper (for Fetch API) ───────
    // Put csrf_meta() in your <head>, then read it in JS:
    // const token = document.querySelector('meta[name="csrf-token"]').content
    public static function meta(): string {
        $token = self::generate();
        return "<meta name=\"csrf-token\" content=\"{$token}\">";
    }
}

// ── Global shorthand helpers ──────────────────
function csrf_field(): string { return CSRFMiddleware::field(); }
function csrf_meta(): string  { return CSRFMiddleware::meta(); }
function csrf_token(): string { return CSRFMiddleware::generate(); }
