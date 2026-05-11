<?php
// ─────────────────────────────────────────────
//  core/App.php — Application Bootstrapper
//  Loads config, registers autoloader, starts
//  session, sets up error handling, runs router.
// ─────────────────────────────────────────────

declare(strict_types=1);

class App {

    private Router $router;

    public function __construct() {
        $this->boot();
        $this->router = new Router();
    }

    // ── Boot sequence ─────────────────────────
    private function boot(): void {
        // 1. Error handling
        ErrorHandler::register();

        // 2. Session
        Session::start();

        // 3. Security headers (XSS, clickjacking, etc.)
        $this->setSecurityHeaders();
    }

    // ── Security Headers (XSS / Clickjacking protection) ──
    private function setSecurityHeaders(): void {
        if (headers_sent()) return;

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

        // Content Security Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://js.pusher.com",
            "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: blob:",
            "connect-src 'self' https://api.paymongo.com https://*.pusher.com wss://*.pusher.com https://cdn.jsdelivr.net",
            "frame-ancestors 'none'",
        ]);
        header("Content-Security-Policy: $csp");
    }

    // ── Register routes & dispatch ────────────
    public function run(): void {
        $app = $this;
        require_once BASE_PATH . '/routes/routes.php';
        $this->router->dispatch();
    }

    public function getRouter(): Router {
        return $this->router;
    }
}