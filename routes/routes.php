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

$router->post('/pusher/auth', [PusherController::class, 'auth']);

// ── Email Verification ────────────────────────
$router->get ('/verify-email',         [VerificationController::class, 'verify']);
$router->post('/resend-verification',  [AuthController::class, 'resendVerification']);

// ── PSGC Proxy (public, server-side — avoids CSP) ─
$router->get('/api/psgc/municipalities', [PsgcController::class, 'municipalities']);
$router->get('/api/psgc/barangays',      [PsgcController::class, 'barangays']);

// ── Contact (public — guests can submit) ──────
$router->get ('/contact', [MessageController::class, 'contact']);
$router->post('/contact', [MessageController::class, 'store']);

// ── Profile (auth required) ───────────────────
$router->get ('/profile',          [AuthController::class, 'profileForm']);
$router->post('/profile',          [AuthController::class, 'updateProfile']);
$router->post('/profile/password', [AuthController::class, 'updatePassword']);

$router->get ('/admin/backup',         [BackupController::class, 'index']);
    $router->get ('/admin/backup/download', [BackupController::class, 'download']);
    $router->post('/admin/backup/restore',  [BackupController::class, 'restore']);

// ══════════════════════════════════════════════
//  CUSTOMER ROUTES (must be logged in)
// ══════════════════════════════════════════════
$router->group('/shop', function(Router $router) {

    // 1. Exact static routes FIRST
    $router->get('',               [ProductController::class,  'catalog']);
    $router->get('/search',        [ProductController::class,  'search']);

    // 2. Cart routes (must be before /{slug} wildcard)
    $router->get ('/cart',         [OrderController::class,    'cart']);
    $router->post('/cart/add',     [OrderController::class,    'addToCart']);
    $router->post('/cart/update',  [OrderController::class,    'updateCart']);
    $router->post('/cart/remove',  [OrderController::class,    'removeFromCart']);
    $router->post('/cart/clear',   [OrderController::class,    'clearCart']);

    // 3. Checkout (must be before /{slug} wildcard)
    $router->get ('/checkout',     [OrderController::class,    'checkoutForm']);
    $router->post('/checkout',     [OrderController::class,    'checkout']);

    // 4. Product JSON for modal (must be before /{slug} wildcard)
    $router->get('/product/{id}',  [ProductController::class,  'productJson']);

    // 5. Wildcard LAST — catches /shop/{slug} for product detail
    $router->get('/{slug}',        [ProductController::class,  'show']);

});

// ── Customer Orders ───────────────────────────
$router->group('/orders', function(Router $router) {
    $router->get('',               [OrderController::class, 'myOrders']);
    $router->get('/{id}',          [OrderController::class, 'show']);
    $router->post('/{id}/cancel',  [OrderController::class, 'cancel']);
});

// ── Payment (PayMongo callbacks) ──────────────
$router->get ('/payment/success',  [PaymentController::class, 'success']);
$router->get ('/payment/failed',   [PaymentController::class, 'failed']);
$router->get ('/payment/cancel',   [PaymentController::class, 'cancel']);
$router->post ('/payment/retry/{id}',    [PaymentController::class, 'retry']);
$router->post('/webhook/paymongo', [PaymentController::class, 'webhook']);

// ── Promo Code Validation (AJAX, auth required) ─
$router->post('/promo/validate',   [PromoController::class, 'validate']);


// ══════════════════════════════════════════════
//  ADMIN / STAFF ROUTES
// ══════════════════════════════════════════════

$router->group('/admin', function(Router $router) {

    // Dashboard
    $router->get('/dashboard',     [DashboardController::class, 'index']);

    // Products
    $router->get ('/products',                [ProductController::class, 'adminIndex']);
    $router->get ('/products/search',         [ProductController::class, 'adminSearch']);
    $router->get ('/products/create',         [ProductController::class, 'create']);
    $router->post('/products/create',         [ProductController::class, 'store']);
    $router->get ('/products/{id}/edit',      [ProductController::class, 'edit']);
    $router->post('/products/{id}/edit',      [ProductController::class, 'update']);
    $router->post('/products/{id}/delete',    [ProductController::class, 'destroy']);

    // Orders
    $router->get ('/orders',               [OrderController::class, 'adminIndex']);
    $router->get ('/orders/{id}',          [OrderController::class, 'adminShow']);
    $router->post('/orders/{id}/status',   [OrderController::class, 'updateStatus']);

    // Inventory
    $router->get ('/inventory',            [ProductController::class, 'inventory']);
    $router->post('/inventory/{id}/stock', [ProductController::class, 'updateStock']);

    // Promo Codes
    $router->get ('/promos',               [PromoController::class, 'index']);
    $router->get ('/promos/create',        [PromoController::class, 'create']);
    $router->post('/promos/store',         [PromoController::class, 'store']);
    $router->get ('/promos/{id}/edit',     [PromoController::class, 'edit']);
    $router->post('/promos/{id}/update',   [PromoController::class, 'update']);
    $router->post('/promos/{id}/delete',   [PromoController::class, 'destroy']);

    // Users
    $router->get ('/users',                [AdminController::class, 'users']);
    $router->post('/users/{id}/role',      [AdminController::class, 'updateRole']);
    $router->post('/users/{id}/toggle',    [AdminController::class, 'toggleActive']);
    $router->post('/users/{id}/delete',    [AdminController::class, 'destroy']);

    // Inquiries
    $router->get ('/inquiries',            [MessageController::class, 'index']);
    $router->get ('/inquiries/{id}',       [MessageController::class, 'show']);
    $router->post('/inquiries/{id}/reply', [MessageController::class, 'reply']);
    $router->post('/inquiries/{id}/status',[MessageController::class, 'updateStatus']);

    // Analytics API
    $router->get('/api/sales-chart',       [DashboardController::class, 'salesChart']);
    $router->get('/api/revenue-chart',     [DashboardController::class, 'revenueChart']);
    $router->get('/api/inventory-chart',   [DashboardController::class, 'inventoryChart']);
    $router->get('/api/best-sellers',      [DashboardController::class, 'bestSellers']);
    $router->get('/api/status-chart',      [DashboardController::class, 'statusChart']);

   

});