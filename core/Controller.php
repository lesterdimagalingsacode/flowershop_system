<?php
// ─────────────────────────────────────────────
//  core/Controller.php — Base Controller
// ─────────────────────────────────────────────

declare(strict_types=1);

abstract class Controller {

    // ── Render a view ─────────────────────────
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void {
        extract($data, EXTR_SKIP);

        ob_start();
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: $viewFile");
        }
        require $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = VIEW_PATH . '/layouts/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new RuntimeException("Layout not found: $layoutFile");
            }
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    // ── JSON responses ────────────────────────
    protected function json(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function jsonSuccess(mixed $data = null, string $message = 'Success', int $status = 200): void {
        $this->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    protected function jsonError(string $message, int $status = 400, mixed $errors = null): void {
        $this->json(['success' => false, 'message' => $message, 'errors' => $errors], $status);
    }

    // ── Redirects ─────────────────────────────
    protected function redirect(string $path): void {
        header('Location: ' . APP_URL . $path);
        exit;
    }

    protected function redirectBack(): void {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
        header('Location: ' . $referer);
        exit;
    }

    protected function flashRedirect(string $path, string $message, string $type = 'success'): void {
        Session::flash('message', $message, $type);
        $this->redirect($path);
    }

    // ── Request helpers ───────────────────────
    protected function input(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function post(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = null): mixed {
        return $_GET[$key] ?? $default;
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function isFetch(): bool {
        return isset($_SERVER['HTTP_ACCEPT'])
            && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
    }

    // ── Auth guards ───────────────────────────
    protected function requireAuth(): void {
        if (!Session::isLoggedIn()) {
            Session::flash('message', 'Please login to continue.', 'warning');
            $this->redirect('/login');
        }
    }

    protected function requireGuest(): void {
        if (Session::isLoggedIn()) {
            $role = Session::userRole();
            $this->redirect($role === ROLE_CUSTOMER ? '/shop' : '/admin/dashboard');
        }
    }

    protected function requireAdmin(): void {
        $this->requireAuth();
        if (!Session::isAdmin()) {
            $this->abort(403);
        }
    }

    protected function requireStaff(): void {
        $this->requireAuth();
        if (!Session::isStaff()) {
            $this->abort(403);
        }
    }

    // ── Abort ─────────────────────────────────
    protected function abort(int $code): void {
        http_response_code($code);
        $file = VIEW_PATH . "/errors/$code.php";
        if (file_exists($file)) require $file;
        else echo "<h1>$code Error</h1>";
        exit;
    }

    // ── Pagination ────────────────────────────
    protected function paginate(int $total, int $perPage = ITEMS_PER_PAGE): Paginator {
        $page = max(1, (int)($this->get('page', 1)));
        return new Paginator($total, $perPage, $page);
    }
}