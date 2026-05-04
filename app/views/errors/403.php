<?php $title = '403 — Forbidden'; ?>

<div class="min-h-[80vh] flex items-center justify-center px-6">
    <div class="text-center max-w-md">

        <!-- Error code -->
        <p class="text-[8rem] font-bold text-forest/10 leading-none select-none"
           style="font-family: var(--font-display);">403</p>

        <!-- Icon -->
        <div class="w-16 h-16 rounded-full bg-gold-lt border border-gold/20 flex items-center justify-center text-2xl mx-auto -mt-8 mb-6">
            🔒
        </div>

        <h1 class="text-3xl text-text mb-3" style="font-family: var(--font-display);">
            Access Denied
        </h1>
        <p class="text-muted text-sm leading-relaxed mb-8">
            You don't have permission to view this page. If you think this is a mistake, please contact support or sign in with the correct account.
        </p>

        <div class="flex gap-3 justify-center flex-wrap">
            <a href="<?= APP_URL ?>/"
               class="bg-forest hover:bg-pine text-white text-sm font-medium px-6 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                Go Home
            </a>
            <?php if (!Session::isLoggedIn()): ?>
            <a href="<?= APP_URL ?>/login"
               class="bg-white border border-border text-text hover:border-forest hover:text-forest text-sm font-medium px-6 py-2.5 rounded-full transition-all shadow-sm">
                Sign In
            </a>
            <?php endif; ?>
        </div>

    </div>
</div>