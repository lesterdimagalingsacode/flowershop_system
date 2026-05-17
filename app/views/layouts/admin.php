<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' — ' : '' ?>Admin · Petal & Soul</title>

    <?= csrf_meta() ?>

    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
		
    <style>
        .sidebar-link.active { background: var(--color-cream); color: var(--color-forest); font-weight: 600; }
        .sidebar-link.active .sidebar-icon { color: var(--color-forest); }

        /* New order badge pulse */
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.2); }
        }
        .badge-pulse { animation: pulse-badge 1s ease-in-out 3; }
    </style>
</head>
<body class="bg-cream text-text min-h-screen" style="font-family: var(--font-body);">

<div class="flex min-h-screen">

    <!-- ── Sidebar ── -->
    <aside class="w-60 bg-white border-r border-border flex flex-col fixed top-0 left-0 h-screen z-40">

        <!-- Logo -->
        <div class="px-6 py-5 border-b border-border">
            <a href="<?= APP_URL ?>/" class="flex items-center gap-2 no-underline">
                <span class="text-lg"></span>
                <div>
                    <div class="text-sm font-bold text-forest tracking-wide" style="font-family: var(--font-display);">Petal & Soul</div>
                    <div class="text-[0.6rem] text-muted uppercase tracking-widest">Admin Panel</div>
                </div>
            </a>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 overflow-y-auto">

            <p class="text-[0.6rem] uppercase tracking-widest text-muted px-3 mb-2">Overview</p>
            <a href="<?= APP_URL ?>/admin/dashboard"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted hover:bg-cream hover:text-forest transition-all mb-1 <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/dashboard') ? 'active' : '' ?>">
                <svg class="sidebar-icon w-4 h-4 text-muted/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-3a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
                </svg>
                Dashboard
            </a>

            <p class="text-[0.6rem] uppercase tracking-widest text-muted px-3 mb-2 mt-4">Catalog</p>
            <a href="<?= APP_URL ?>/admin/products"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted hover:bg-cream hover:text-forest transition-all mb-1 <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/products') ? 'active' : '' ?>">
                <svg class="sidebar-icon w-4 h-4 text-muted/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Products
            </a>
            <a href="<?= APP_URL ?>/admin/inventory"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted hover:bg-cream hover:text-forest transition-all mb-1 <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/inventory') ? 'active' : '' ?>">
                <svg class="sidebar-icon w-4 h-4 text-muted/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Inventory
            </a>

            <a href="<?= APP_URL ?>/admin/promos"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted hover:bg-cream hover:text-forest transition-all mb-1 <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/promos') ? 'active' : '' ?>">
                <svg class="sidebar-icon w-4 h-4 text-muted/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5l9 9a2 2 0 010 2.828l-5.172 5.172a2 2 0 01-2.828 0L4 11V3h3zM7 7a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
                Promo Codes
            </a>

            <p class="text-[0.6rem] uppercase tracking-widest text-muted px-3 mb-2 mt-4">Orders</p>
            <a href="<?= APP_URL ?>/admin/orders"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted hover:bg-cream hover:text-forest transition-all mb-1 <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/orders')) ? 'active' : '' ?>">
                <svg class="sidebar-icon w-4 h-4 text-muted/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Orders</span>
                <!-- New order badge — shown by Pusher when a new order arrives -->
                <span id="new-order-badge"
                      class="hidden ml-auto bg-red-500 text-white text-[0.55rem] font-bold rounded-full min-w-[1.1rem] h-[1.1rem] flex items-center justify-center px-1">
                    0
                </span>
            </a>

            <p class="text-[0.6rem] uppercase tracking-widest text-muted px-3 mb-2 mt-4">Admin</p>
            <a href="<?= APP_URL ?>/admin/users"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted hover:bg-cream hover:text-forest transition-all mb-1 <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/users') ? 'active' : '' ?>">
                    <svg class="sidebar-icon w-4 h-4 text-muted/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                Users
            </a>
            <a href="<?= APP_URL ?>/admin/backup"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted hover:bg-cream hover:text-forest transition-all mb-1 <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/backup') ? 'active' : '' ?>">
                <svg class="sidebar-icon w-4 h-4 text-muted/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Backup & Restore
            </a>

            <a href="<?= APP_URL ?>/admin/logs"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted hover:bg-cream hover:text-forest transition-all mb-1 <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/logs') ? 'active' : '' ?>">
                <svg class="sidebar-icon w-4 h-4 text-muted/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Activity Logs
            </a>
        </nav>

        <!-- User info -->
        <div class="px-4 py-4 border-t border-border">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gold-lt border border-forest/20 flex items-center justify-center text-xs font-bold text-forest flex-shrink-0">
                    <?= strtoupper(substr(Session::user()['first_name'] ?? Session::user()['name'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-text truncate"><?= e(Session::user()['first_name'] ?? explode(' ', Session::user()['name'] ?? 'Admin')[0]) ?></div>
                    <div class="text-[0.65rem] text-muted capitalize"><?= e(Session::user()['role'] ?? 'staff') ?></div>
                </div>
                <form method="POST" action="<?= APP_URL ?>/logout" class="ml-auto flex-shrink-0">
                    <?= csrf_field() ?>
                    <button type="submit" title="Logout" class="text-muted hover:text-forest transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ── Main content ── -->
    <div class="flex-1 ml-60 flex flex-col min-h-screen">

        <!-- Top bar -->
        <header class="bg-white border-b border-border px-8 py-4 flex items-center justify-between sticky top-0 z-30">
            <div>
                <h1 class="text-base font-semibold text-text" style="font-family: var(--font-display);"><?= isset($title) ? e($title) : 'Admin' ?></h1>
            </div>
            <a href="<?= APP_URL ?>/shop" target="_blank"
               class="flex items-center gap-1.5 text-xs text-muted hover:text-forest transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                View Shop
            </a>
        </header>

        <!-- Flash messages -->
        <?php $flash = Session::getAllFlash(); ?>
        <?php if (!empty($flash)): ?>
            <div class="px-8 pt-5">
                <?php foreach ($flash as $f):
                    $msg = is_string($f['message']) ? $f['message'] : '';
                    if (!$msg) continue;
                    $cls = match($f['type'] ?? 'info') {
                        'success' => 'bg-green-50 border-green-200 text-green-800',
                        'error'   => 'bg-red-50 border-red-200 text-red-800',
                        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                        default   => 'bg-blue-50 border-blue-200 text-blue-800',
                    };
                    $icon = match($f['type'] ?? 'info') {
                        'success' => '✓', 'error' => '✕', 'warning' => '!', default => 'i',
                    };
                ?>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border mb-2 text-sm <?= $cls ?>">
                        <span class="font-bold w-4 text-center"><?= $icon ?></span>
                        <span><?= e($msg) ?></span>
                        <button onclick="this.parentElement.remove()" class="ml-auto opacity-50 hover:opacity-100 transition-opacity">✕</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Page content -->
        <main class="flex-1 px-8 py-6">
            <?= $content ?>
        </main>

        <footer class="px-8 py-4 border-t border-border">
            <p class="text-xs text-muted">© <?= date('Y') ?> Petal & Soul — Admin Panel</p>
        </footer>
    </div>
</div>

<script src="<?= APP_URL ?>/js/toast.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="<?= APP_URL ?>/js/app.js"></script>

<!-- ── Pusher: new order notifications for admin ── -->

<script>
(function () {
    const PUSHER_KEY     = '<?= PUSHER_APP_KEY ?>';
    const PUSHER_CLUSTER = '<?= PUSHER_APP_CLUSTER ?>';

    if (!PUSHER_KEY) return;

    const pusher = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_CLUSTER,
        authEndpoint: '<?= APP_URL ?>/pusher/auth',
        auth: {
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            }
        }
    });
    const adminChannel = pusher.subscribe('private-admin');

    let newOrderCount = 0;
    const badge       = document.getElementById('new-order-badge');

    // ── New order placed by customer ──
    adminChannel.bind('new-order', function (data) {
        newOrderCount++;

        // Update badge
        if (badge) {
            badge.textContent = newOrderCount;
            badge.classList.remove('hidden');
            badge.classList.add('badge-pulse');
            setTimeout(function () { badge.classList.remove('badge-pulse'); }, 3100);
        }

        // Toast notification
        if (typeof Toast !== 'undefined') {
            Toast.success(
                '🌸 New order #' + data.order_number +
                ' from ' + data.customer +
                ' — ₱' + parseFloat(data.total_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })
            );
        }
    });

    // ── Order status changed — refresh dashboard stats if on dashboard ──
    adminChannel.bind('order-status-changed', function (data) {
        // If a refreshStats function exists on the current page (dashboard), call it
        if (typeof window.refreshDashboardStats === 'function') {
            window.refreshDashboardStats();
        }
    });

    // ── Clear badge when navigating to orders page ──
    const ordersLink = document.querySelector('a[href*="/admin/orders"]');
    if (ordersLink) {
        ordersLink.addEventListener('click', function () {
            newOrderCount = 0;
            if (badge) badge.classList.add('hidden');
        });
    }
})();
</script>

</body>
</html>