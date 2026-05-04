<?php $title = '500 — Server Error'; ?>

<div class="min-h-[80vh] flex items-center justify-center px-6">
    <div class="text-center max-w-md">

        <!-- Error code -->
        <p class="text-[8rem] font-bold text-forest/10 leading-none select-none"
           style="font-family: var(--font-display);">500</p>

        <!-- Icon -->
        <div class="w-16 h-16 rounded-full bg-gold-lt border border-gold/20 flex items-center justify-center text-2xl mx-auto -mt-8 mb-6">
            🌧️
        </div>

        <h1 class="text-3xl text-text mb-3" style="font-family: var(--font-display);">
            Something Went Wrong
        </h1>
        <p class="text-muted text-sm leading-relaxed mb-8">
            Our server ran into an unexpected problem. Our team has been notified. Please try again in a moment.
        </p>

        <div class="flex gap-3 justify-center flex-wrap">
            <a href="<?= APP_URL ?>/"
               class="bg-forest hover:bg-pine text-white text-sm font-medium px-6 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                Go Home
            </a>
            <button onclick="window.location.reload()"
               class="bg-white border border-border text-text hover:border-forest hover:text-forest text-sm font-medium px-6 py-2.5 rounded-full transition-all shadow-sm cursor-pointer">
                Try Again
            </button>
        </div>

        <?php if (defined('APP_DEBUG') && APP_DEBUG && isset($exception)): ?>
        <!-- Debug info — only shown in dev mode -->
        <div class="mt-10 text-left bg-bark/5 border border-border rounded-2xl p-5 text-xs font-mono text-muted overflow-auto max-h-48">
            <p class="font-semibold text-text mb-2">Debug Info</p>
            <p><span class="text-forest">Message:</span> <?= e($exception->getMessage()) ?></p>
            <p><span class="text-forest">File:</span> <?= e($exception->getFile()) ?></p>
            <p><span class="text-forest">Line:</span> <?= e((string)$exception->getLine()) ?></p>
        </div>
        <?php endif; ?>

    </div>
</div>