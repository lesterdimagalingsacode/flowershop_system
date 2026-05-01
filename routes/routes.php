<?php
// ─────────────────────────────────────────────
//  routes/routes.php — All Application Routes
// ─────────────────────────────────────────────

/** @var Router $router */
$router = $app->getRouter();

// ══════════════════════════════════════════════
//  PUBLIC ROUTES (no auth required)
// ══════════════════════════════════════════════

// Landing page
$router->get('/', [LandingController::class, 'index']);

// ── Auth ──────────────────────────────────────
$router->get ('/login',    [AuthController::class, 'loginForm']);
$router->post('/login',    [AuthController::class, 'login']);
$router->get ('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout',   [AuthController::class, 'logout']);

// ══════════════════════════════════════════════
//  CUSTOMER ROUTES (must be logged in)
// ══════════════════════════════════════════════
$router->group('/shop', function(Router $router) {

    $router->get('',               [ProductController::class,  'catalog']);
    $router->get('/{slug}',        [ProductController::class,  'show']);

    $router->get('/cart',          [OrderController::class,    'cart']);
    $router->post('/cart/add',     [OrderController::class,    'addToCart']);
    $router->post('/cart/update',  [OrderController::class,    'updateCart']);
    $router->post('/cart/remove',  [OrderController::class,    'removeFromCart']);
    $router->post('/cart/clear',   [OrderController::class,    'clearCart']);

    $router->get ('/checkout',     [OrderController::class,    'checkoutForm']);
    $router->post('/checkout',     [OrderController::class,    'checkout']);

});

// ── Customer Orders ───────────────────────────
$router->group('/orders', function(Router $router) {

    $router->get('',               [OrderController::class,    'myOrders']);
    $router->get('/{id}',          [OrderController::class,    'show']);
    $router->post('/{id}/cancel',  [OrderController::class,    'cancel']);

});

// ── Payment ───────────────────────────────────
$router->get ('/payment/success',  [PaymentController::class,  'success']);
$router->get ('/payment/failed',   [PaymentController::class,  'failed']);
$router->post('/webhook/paymongo', [PaymentController::class,  'webhook']);


//ADMIN / STAFF ROUTES

$router->group('/admin', function(Router $router) {

    // Dashboard
    $router->get('/dashboard',     [DashboardController::class, 'index']);

    // Products
    $router->get ('/products',             [ProductController::class, 'adminIndex']);
    $router->get ('/products/create',      [ProductController::class, 'create']);
    $router->post('/products/create',      [ProductController::class, 'store']);
    $router->get ('/products/{id}/edit',   [ProductController::class, 'edit']);
    $router->post('/products/{id}/edit',   [ProductController::class, 'update']);
    $router->post('/products/{id}/delete', [ProductController::class, 'destroy']);

    // Orders
    $router->get ('/orders',               [OrderController::class,   'adminIndex']);
    $router->get ('/orders/{id}',          [OrderController::class,   'adminShow']);
    $router->post('/orders/{id}/status',   [OrderController::class,   'updateStatus']);

    // Inventory
    $router->get ('/inventory',            [ProductController::class, 'inventory']);
    $router->post('/inventory/{id}/stock', [ProductController::class, 'updateStock']);

    // Users (admin only)
    $router->get ('/users',                [AdminController::class,   'users']);
    $router->post('/users/{id}/role',      [AdminController::class,   'updateRole']);
    $router->post('/users/{id}/toggle',    [AdminController::class,   'toggleActive']);
    $router->post('/users/{id}/delete',    [AdminController::class,   'destroy']);

    // Analytics API (Fetch API endpoints for Chart.js)
    $router->get('/api/sales-chart',       [DashboardController::class, 'salesChart']);
    $router->get('/api/inventory-chart',   [DashboardController::class, 'inventoryChart']);
    $router->get('/api/revenue-chart',     [DashboardController::class, 'revenueChart']);

});
