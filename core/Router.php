<?php
// ─────────────────────────────────────────────
//  core/Router.php — RESTful Router
//  • GET, POST, PUT, PATCH, DELETE
//  • Route parameters  /orders/{id}
//  • Middleware groups
//  • 404 / 405 handling
// ─────────────────────────────────────────────

declare(strict_types=1);

class Router {

    private array $routes     = [];
    private array $middleware = [];
    private string $prefix    = '';

    // ── Route Registration ────────────────────
    public function get(string $path, array|callable $handler): self {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array|callable $handler): self {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array|callable $handler): self {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, array|callable $handler): self {
        return $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, array|callable $handler): self {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /** Shorthand: GET + POST on same path */
    public function form(string $path, array $handler): self {
        $this->get($path, $handler);
        $this->post($path, $handler);
        return $this;
    }

    // ── Middleware ────────────────────────────
    public function middleware(string|array $middleware): self {
        $clone = clone $this;
        $clone->middleware = array_merge(
            $this->middleware,
            (array) $middleware
        );
        return $clone;
    }

    public function group(string $prefix, callable $callback): void {
        $previousPrefix     = $this->prefix;
        $previousMiddleware = $this->middleware;

        $this->prefix = $previousPrefix . $prefix;
        $callback($this);

        $this->prefix     = $previousPrefix;
        $this->middleware = $previousMiddleware;
    }

    // ── Dispatch ──────────────────────────────
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        // Support PUT / PATCH / DELETE via POST + _method override
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = $this->parseUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $params = $this->matchPath($route['path'], $uri);
            if ($params === null) continue;

            // ── Run middleware ─────────────────
            foreach ($route['middleware'] as $mw) {
                $this->runMiddleware($mw, $params);
            }

            // ── Call handler ───────────────────
            $this->callHandler($route['handler'], $params);
            return;
        }

        // Check if path exists but wrong method → 405
        foreach ($this->routes as $route) {
            if ($this->matchPath($route['path'], $uri) !== null) {
                $this->abort(405);
                return;
            }
        }

        $this->abort(404);
    }

    // ── Helpers ───────────────────────────────
    private function addRoute(string $method, string $path, array|callable $handler): self {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $this->prefix . $path,
            'handler'    => $handler,
            'middleware' => $this->middleware,
        ];
        return $this;
    }

    private function parseUri(): string {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rawurldecode($uri);

        // Derive base path from APP_URL (works on both ngrok and InfinityFree)
        $basePath = rtrim(parse_url(APP_URL, PHP_URL_PATH) ?? '', '/');

        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        return '/' . ltrim($uri, '/');
    }

    /**
     * Match a route pattern against the current URI.
     * Returns named params array on match, null on no match.
     * Pattern: /orders/{id}  →  ['id' => '42']
     */
    private function matchPath(string $pattern, string $uri): ?array {
        $pattern = rtrim($pattern, '/') ?: '/';
        $uri     = rtrim($uri, '/')     ?: '/';

        $regex = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#i';

        if (!preg_match($regex, $uri, $matches)) return null;

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    private function runMiddleware(string $name, array $params): void {
        $map = [
            'auth'     => 'AuthMiddleware',
            'admin'    => 'AuthMiddleware',
            'staff'    => 'AuthMiddleware',
            'guest'    => 'AuthMiddleware',
            'csrf'     => 'CSRFMiddleware',
            'throttle' => 'RateLimiter',
        ];

        $class = $map[$name] ?? $name;
        if (!class_exists($class)) return;

        $mw = new $class();
        $mw->handle($name, $params);
    }

    private function callHandler(array|callable $handler, array $params): void {
        if (is_callable($handler)) {
            call_user_func($handler, $params);
            return;
        }

        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            throw new RuntimeException("Controller not found: $controllerClass");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new RuntimeException("Method $method not found in $controllerClass");
        }

        $controller->$method($params);
    }

    private function abort(int $code): void {
        http_response_code($code);
        $view = VIEW_PATH . "/errors/$code.php";
        if (file_exists($view)) {
            require $view;
        } else {
            echo "<h1>$code Error</h1>";
        }
        exit;
    }
}
