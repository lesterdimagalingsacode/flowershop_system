<?php
// ─────────────────────────────────────────────
//  middleware/AuthMiddleware.php — RBAC
//  Checks session role before every
//  protected route runs.
// ─────────────────────────────────────────────

declare(strict_types=1);

class AuthMiddleware {

    public function handle(string $role, array $params = []): void {
        match ($role) {
            'auth'     => $this->requireAuth(),
            'guest'    => $this->requireGuest(),
            'admin'    => $this->requireRole(ROLE_ADMIN),
            'staff'    => $this->requireStaff(),
            'customer' => $this->requireRole(ROLE_CUSTOMER),
            default    => null,
        };
    }

    // ── Must be logged in ─────────────────────
    private function requireAuth(): void {
        if (!Session::isLoggedIn()) {
            Session::flash('message', 'Please login to continue.', 'warning');
            $this->redirect('/login');
        }
    }

    // ── Must NOT be logged in (login/register pages) ──
    private function requireGuest(): void {
        if (Session::isLoggedIn()) {
            $role = Session::userRole();
            $this->redirect($role === ROLE_CUSTOMER ? '/shop' : '/admin/dashboard');
        }
    }

    // ── Admin OR Staff ────────────────────────
    private function requireStaff(): void {
        $this->requireAuth();
        if (!Session::isStaff()) {
            $this->abort(403);
        }
    }

    // ── Exact role match ──────────────────────
    private function requireRole(string $required): void {
        $this->requireAuth();
        if (Session::userRole() !== $required) {
            $this->abort(403);
        }
    }

    // ── Helpers ───────────────────────────────
    private function redirect(string $path): void {
        header('Location: ' . APP_URL . $path);
        exit;
    }

    private function abort(int $code): void {
        http_response_code($code);
        $file = VIEW_PATH . "/errors/$code.php";
        if (file_exists($file)) require $file;
        else echo "<h1>$code — Access Denied</h1>";
        exit;
    }
}
