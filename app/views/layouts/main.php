<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' — ' : '' ?>Petal & Soul</title>

    <?= csrf_meta() ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (compiled by CLI) -->
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
</head>
<body class="bg-deep text-petal min-h-screen flex flex-col font-(--font-body)">

    <!-- ── Navbar ── -->
    <nav class="sticky top-0 z-50 bg-deep/85 backdrop-blur-md border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="<?= APP_URL ?>/" class="flex items-center gap-2 no-underline">
                    <span class="text-2xl leading-none">🌸</span>
                    <span class="font-(--font-display) text-xl font-semibold text-petal tracking-wide">Petal & Soul</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-7">
                    <a href="<?= APP_URL ?>/shop" class="text-muted hover:text-petal text-sm font-medium tracking-wide transition-colors">Shop</a>

                    <?php if (Session::isLoggedIn()): ?>
                        <a href="<?= APP_URL ?>/orders" class="text-muted hover:text-petal text-sm font-medium tracking-wide transition-colors">My Orders</a>

                        <?php if (Session::isStaff()): ?>
                            <a href="<?= APP_URL ?>/admin/dashboard" class="text-muted hover:text-petal text-sm font-medium tracking-wide transition-colors">Dashboard</a>
                        <?php endif; ?>

                        <!-- Cart -->
                        <a href="<?= APP_URL ?>/shop/cart" class="relative text-muted hover:text-petal transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13L5.4 5M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                            </svg>
                            <?php $cartCount = array_sum(array_column(Session::getCart(), 'quantity')); ?>
                            <?php if ($cartCount > 0): ?>
                                <span class="absolute -top-2 -right-2 bg-rose text-white text-[0.6rem] rounded-full w-4 h-4 flex items-center justify-center">
                                    <?= $cartCount ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- User dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-muted hover:text-petal text-sm font-medium transition-colors">
                                <span class="w-7 h-7 rounded-full bg-surface border border-subtle flex items-center justify-content font-semibold text-xs text-petal">
                                    <?= strtoupper(substr(Session::user()['name'], 0, 1)) ?>
                                </span>
                                <span><?= e(explode(' ', Session::user()['name'])[0]) ?></span>
                                <svg class="w-3 h-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-44 bg-card border border-border rounded-xl shadow-2xl py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="<?= APP_URL ?>/profile" class="block px-4 py-2 text-sm text-muted hover:text-petal hover:bg-white/5 rounded-lg mx-1 transition-colors">
                                    My Profile
                                </a>
                                <hr class="border-border my-1">
                                <form method="POST" action="<?= APP_URL ?>/logout">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-muted hover:text-petal hover:bg-white/5 rounded-lg mx-1 transition-colors block" style="width:calc(100% - 8px);">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                    <?php else: ?>
                        <a href="<?= APP_URL ?>/login" class="text-muted hover:text-petal text-sm font-medium tracking-wide transition-colors">Login</a>
                        <a href="<?= APP_URL ?>/register" class="bg-rose hover:bg-rose/85 text-white text-sm font-semibold px-5 py-2.5 rounded-full transition-all hover:-translate-y-px">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile hamburger -->
                <button id="mobile-menu-btn" class="md:hidden text-muted hover:text-petal transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4 pt-3 border-t border-border">
                <div class="flex flex-col gap-4 text-sm font-medium">
                    <a href="<?= APP_URL ?>/shop" class="text-muted hover:text-petal transition-colors">Shop</a>
                    <?php if (Session::isLoggedIn()): ?>
                        <a href="<?= APP_URL ?>/orders" class="text-muted hover:text-petal transition-colors">My Orders</a>
                        <?php if (Session::isStaff()): ?>
                            <a href="<?= APP_URL ?>/admin/dashboard" class="text-muted hover:text-petal transition-colors">Dashboard</a>
                        <?php endif; ?>
                        <form method="POST" action="<?= APP_URL ?>/logout">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-muted hover:text-petal transition-colors text-left text-sm font-medium">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/login" class="text-muted hover:text-petal transition-colors">Login</a>
                        <a href="<?= APP_URL ?>/register" class="bg-rose text-white px-5 py-2 rounded-full text-sm font-semibold w-fit">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ── Flash Messages ── -->
    <?php $flash = Session::getAllFlash(); ?>
    <?php if (!empty($flash)): ?>
        <div id="flash-container" class="max-w-7xl mx-auto px-4 pt-4 w-full">
            <?php foreach ($flash as $key => $f):
                $msg = is_string($f['message']) ? $f['message'] : '';
                if (!$msg) continue;
                $cls = match($f['type'] ?? 'info') {
                    'success' => 'bg-green-950 border-green-800 text-green-300',
                    'error'   => 'bg-red-950 border-red-800 text-red-300',
                    'warning' => 'bg-yellow-950 border-yellow-800 text-yellow-300',
                    default   => 'bg-blue-950 border-blue-800 text-blue-300',
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
    <footer class="bg-card border-t border-border">
        <div class="max-w-7xl mx-auto px-4 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <a href="<?= APP_URL ?>/" class="flex items-center gap-2 no-underline">
                    <span class="text-xl">🌸</span>
                    <span class="font-(--font-display) text-lg text-petal tracking-wide">Petal & Soul</span>
                </a>
                <p class="text-xs text-muted text-center">
                    Every bloom tells a story. © <?= date('Y') ?> Petal & Soul.
                </p>
                <div class="flex items-center gap-5">
                    <a href="<?= APP_URL ?>/shop"     class="text-xs text-muted hover:text-petal transition-colors">Shop</a>
                    <a href="<?= APP_URL ?>/login"    class="text-xs text-muted hover:text-petal transition-colors">Login</a>
                    <a href="<?= APP_URL ?>/register" class="text-xs text-muted hover:text-petal transition-colors">Register</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- App JS -->
    <script src="<?= APP_URL ?>/js/app.js"></script>
    <script src="<?= APP_URL ?>/js/toast.js"></script>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            document.getElementById('mobile-menu')?.classList.toggle('hidden');
        });
        setTimeout(() => {
            document.querySelectorAll('#flash-container > div').forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
    </script>
</body>
</html>