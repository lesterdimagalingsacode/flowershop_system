<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' — ' : '' ?>Petal & Soul</title>

    <?= csrf_meta() ?>

    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
</head>
<body class="bg-cream text-text min-h-screen flex flex-col" style="font-family: var(--font-body);">

    <!-- ── Navbar ── -->
    <nav class="bg-white border-b border-border sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="<?= APP_URL ?>/" class="flex items-center gap-2 no-underline">
                    <span class="text-xl font-bold text-forest tracking-wide" style="font-family: var(--font-display);">Petal & Soul</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="<?= APP_URL ?>/shop" class="text-sm font-medium text-muted hover:text-forest transition-colors tracking-wide">Shop</a>

                    <?php if (Session::isLoggedIn()): ?>
                        <a href="<?= APP_URL ?>/orders" class="text-sm font-medium text-muted hover:text-forest transition-colors tracking-wide">My Orders</a>

                        <?php if (Session::isStaff()): ?>
                            <a href="<?= APP_URL ?>/admin/dashboard" class="text-sm font-medium text-muted hover:text-forest transition-colors tracking-wide">Dashboard</a>
                        <?php endif; ?>

                        <!-- Cart -->
                        <a href="<?= APP_URL ?>/shop/cart" class="relative text-muted hover:text-forest transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13L5.4 5M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                            </svg>
                            <?php $cartCount = (new Cart())->count(Session::userId()); ?>
                            <span data-cart-count
                                class="absolute -top-2 -right-2 bg-forest text-white text-[0.6rem] rounded-full w-4 h-4 flex items-center justify-center font-semibold <?= $cartCount === 0 ? 'hidden' : '' ?>">
                                <?= $cartCount ?>
                            </span>
                        </a>

                        <!-- User dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-sm font-medium text-muted hover:text-forest transition-colors">
                                <span class="w-8 h-8 rounded-full bg-gold-lt border border-forest/20 flex items-center justify-center text-xs font-bold text-forest">
                                    <?= strtoupper(substr(Session::user()['first_name'] ?? Session::user()['name'] ?? 'U', 0, 1)) ?>
                                </span>
                                <span><?= e(Session::user()['first_name'] ?? explode(' ', Session::user()['name'] ?? '')[0]) ?></span>
                                <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-44 bg-white border border-border rounded-2xl shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="<?= APP_URL ?>/profile" class="block px-4 py-2.5 text-sm text-muted hover:text-forest hover:bg-cream rounded-xl mx-1 transition-colors">
                                    My Profile
                                </a>
                                <hr class="border-border my-1">
                                <form method="POST" action="<?= APP_URL ?>/logout">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-muted hover:text-forest hover:bg-cream rounded-xl mx-1 transition-colors" style="width:calc(100% - 8px);">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                    <?php else: ?>
                        <a href="<?= APP_URL ?>/login" class="text-sm font-medium text-muted hover:text-forest transition-colors tracking-wide">Login</a>
                        <a href="<?= APP_URL ?>/register" class="bg-forest hover:bg-pine text-white text-sm font-semibold px-5 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile hamburger -->
                <button id="mobile-menu-btn" class="md:hidden text-muted hover:text-forest transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4 pt-3 border-t border-border">
                <div class="flex flex-col gap-4">
                    <a href="<?= APP_URL ?>/shop" class="text-sm font-medium text-muted hover:text-forest transition-colors">Shop</a>
                    <?php if (Session::isLoggedIn()): ?>
                        <a href="<?= APP_URL ?>/shop/cart" class="text-sm font-medium text-muted hover:text-forest transition-colors">Cart</a>
                        <a href="<?= APP_URL ?>/orders" class="text-sm font-medium text-muted hover:text-forest transition-colors">My Orders</a>
                        <?php if (Session::isStaff()): ?>
                            <a href="<?= APP_URL ?>/admin/dashboard" class="text-sm font-medium text-muted hover:text-forest transition-colors">Dashboard</a>
                        <?php endif; ?>
                        <form method="POST" action="<?= APP_URL ?>/logout">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-sm font-medium text-muted hover:text-forest transition-colors text-left">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/login" class="text-sm font-medium text-muted hover:text-forest transition-colors">Login</a>
                        <a href="<?= APP_URL ?>/register" class="bg-forest text-white text-sm font-semibold px-5 py-2 rounded-full w-fit">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ── Flash Messages ── -->
    <?php $flash = Session::getAllFlash(); ?>
    <?php if (!empty($flash)): ?>
        <div id="flash-container" class="max-w-7xl mx-auto px-6 pt-4 w-full">
            <?php foreach ($flash as $key => $f):
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

    <!-- ── Page Content ── -->
    <main class="flex-1">
        <?= $content ?>
    </main>

    <!-- ── Footer ── -->
    <footer class="bg-white border-t border-border mt-0">
        <div class="max-w-7xl mx-auto px-6 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <a href="<?= APP_URL ?>/" class="flex items-center gap-2 no-underline">
                    <span class="text-xl">🌿</span>
                    <span class="text-lg font-semibold text-forest tracking-wide" style="font-family: var(--font-display);">Petal & Soul</span>
                </a>
                <p class="text-xs text-muted text-center">
                    Every bloom tells a story. © <?= date('Y') ?> Petal & Soul.
                </p>
                <div class="flex items-center gap-5">
                    <a href="<?= APP_URL ?>/shop"     class="text-xs text-muted hover:text-forest transition-colors">Shop</a>
                    <a href="<?= APP_URL ?>/login"    class="text-xs text-muted hover:text-forest transition-colors">Login</a>
                    <a href="<?= APP_URL ?>/register" class="text-xs text-muted hover:text-forest transition-colors">Register</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= APP_URL ?>/js/toast.js"></script>
    <script src="<?= APP_URL ?>/js/app.js"></script>

</body>
</html>