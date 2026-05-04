<?php $title = '404 — Not Found'; ?>

<div class="min-h-[80vh] flex items-center justify-center px-6">
    <div class="text-center max-w-md">

        <!-- Error code -->
        <p class="text-[8rem] font-bold text-forest/10 leading-none select-none"
           style="font-family: var(--font-display);">404</p>

        <!-- Icon -->
        <div class="w-16 h-16 rounded-full bg-gold-lt border border-gold/20 flex items-center justify-center text-2xl mx-auto -mt-8 mb-6">
            🌿
        </div>

        <h1 class="text-3xl text-text mb-3" style="font-family: var(--font-display);">
            Flower Not Found
        </h1>
        <p class="text-muted text-sm leading-relaxed mb-8">
            The page you're looking for doesn't exist or may have been moved. Let's get you back to something beautiful.
        </p>

        <div class="flex gap-3 justify-center flex-wrap">
            <a href="<?= APP_URL ?>/"
               class="bg-forest hover:bg-pine text-white text-sm font-medium px-6 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                Go Home
            </a>
            <a href="<?= APP_URL ?>/shop"
               class="bg-white border border-border text-text hover:border-forest hover:text-forest text-sm font-medium px-6 py-2.5 rounded-full transition-all shadow-sm">
                Browse the Shop
            </a>
        </div>

    </div>
</div>