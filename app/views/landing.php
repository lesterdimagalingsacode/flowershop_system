<?php $title = 'Welcome'; ?>

<section class="bg-cream overflow-hidden relative px-6 pt-16 pb-10">

    <!-- Headline -->
    <div class="text-center max-w-4xl mx-auto relative z-10">
        <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-5">
            ✦ &nbsp; Flowers for every soul &nbsp; ✦
        </p>
        <h1 class="leading-tight">
            <span class="block font-bold text-[clamp(3rem,7vw,6rem)] text-forest"
                  style="font-family: var(--font-display);">Blooms that</span>
            <span class="block font-bold text-[clamp(3rem,7vw,6rem)] text-forest"
                  style="font-family: var(--font-display);">created love,</span>
            <span class="block font-bold text-[clamp(3rem,7vw,6rem)] text-forest"
                  style="font-family: var(--font-display);">for Everyone</span>
        </h1>
    </div>

    <!-- 3-column layout: tulip | bouquet (center, overlapping) | CTA -->
    <div class="relative z-20 max-w-6xl mx-auto flex items-end justify-center gap-6 -mt-5">

        <!-- Glow -->
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-72 bg-forest/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- LEFT — Tulips -->
        <div class="hidden md:block w-72 lg:w-80 flex-shrink-0 -translate-y-8">
            <img src="<?= APP_URL ?>/images/tulips.webp"
             alt="Tulips"
             width="320" height="480"
             class="w-full object-contain mix-blend-multiply">
        </div>

        <!-- CENTER — Bouquet -->
        <div class="relative w-64 md:w-80 lg:w-96 rounded-3xl overflow-hidden shadow-2xl flex-shrink-0 -translate-y-4"
             style="box-shadow: 0 40px 80px rgba(30,58,47,0.3);">
            <img src="<?= APP_URL ?>/images/Boquet.webp"
             alt="Bouquet"
             width="400" height="500"
             fetchpriority="high"
             class="w-full object-cover z-50">
            <div class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-sm rounded-2xl px-4 py-3 flex items-center gap-3 shadow-md">
                <span class="text-xl">🌿</span>
                <div>
                    <p class="text-xs font-semibold text-text">Fresh Daily</p>
                    <p class="text-[0.65rem] text-muted">Handpicked blooms</p>
                </div>
            </div>
        </div>

        <!-- RIGHT — CTA block -->
        <div class="hidden md:flex flex-col gap-5 flex-shrink-0 w-56 lg:w-64 pb-10">
            <p class="text-sm text-muted leading-relaxed">
                Whether for a loved one, a friend, or yourself — crafted with intention and soul.
            </p>
            <a href="<?= APP_URL ?>/shop"
               class="bg-forest hover:bg-pine text-white font-medium px-6 py-3 rounded-full text-sm transition-all hover:-translate-y-px shadow-sm text-center">
                Explore the Shop
            </a>
            <a href="<?= APP_URL ?>/register"
               class="bg-white border border-border text-text hover:border-forest hover:text-forest font-medium px-6 py-3 rounded-full text-sm transition-all shadow-sm text-center">
                Create an Account
            </a>
            <div class="bg-white rounded-2xl px-4 py-3 shadow-lg flex items-center gap-3 border border-border">
                <span class="text-xl">💛</span>
                <div>
                    <p class="text-xs font-semibold text-text">Made with love</p>
                    <p class="text-[0.65rem] text-muted">Every arrangement</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Mobile CTA -->
    <div class="md:hidden text-center mt-8 pb-10">
        <p class="text-muted text-sm leading-relaxed max-w-sm mx-auto mb-6">
            Whether for a loved one, a friend, or yourself — crafted with intention and soul.
        </p>
        <div class="flex gap-3 justify-center flex-wrap">
            <a href="<?= APP_URL ?>/shop"
               class="bg-forest hover:bg-pine text-white font-medium px-8 py-3.5 rounded-full text-sm transition-all shadow-sm">
                Explore the Shop
            </a>
            <a href="<?= APP_URL ?>/register"
               class="bg-white border border-border text-text hover:border-forest hover:text-forest font-medium px-8 py-3.5 rounded-full text-sm transition-all shadow-sm">
                Create an Account
            </a>
        </div>
    </div>

</section>

<?php if (!empty($featured)): ?>
<!-- ── Featured Products ──────────────────────────────────────── -->
<section class="bg-white border-t border-border py-16 px-6">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-2">
                    ✦ &nbsp; Fresh picks &nbsp; ✦
                </p>
                <h2 class="text-3xl md:text-4xl text-forest"
                    style="font-family: var(--font-display);">Shop our latest</h2>
            </div>
            <a href="<?= APP_URL ?>/shop"
               class="hidden md:inline-flex items-center gap-2 text-sm font-medium text-forest hover:text-pine transition-colors">
                View all blooms
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <!-- Product grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($featured as $product): ?>
            <?php
                $imageUrl = $product['image']
                    ? APP_URL . '/images/products/' . e($product['image'])
                    : 'https://picsum.photos/seed/' . $product['id'] . '/400/400';
            ?>
            <a href="<?= APP_URL ?>/shop"
               class="group bg-cream border border-border rounded-2xl overflow-hidden hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 block">

                <!-- Image -->
                <div class="relative overflow-hidden aspect-square">
                    <img src="<?= $imageUrl ?>"
                     alt="<?= e($product['name']) ?>"
                     width="400" height="400"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     loading="lazy">

                    <!-- Category badge -->
                    <?php if ($product['category_name']): ?>
                    <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-forest text-[0.6rem] font-semibold px-2.5 py-1 rounded-full">
                        <?= e($product['category_name']) ?>
                    </div>
                    <?php endif; ?>

                    <!-- Low stock badge -->
                    <?php if ($product['stock'] <= $product['low_stock_alert']): ?>
                    <div class="absolute top-3 right-3 bg-gold text-white text-[0.6rem] font-medium px-2.5 py-1 rounded-full">
                        Low Stock
                    </div>
                    <?php endif; ?>

                    <!-- Hover overlay -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300 flex items-center justify-center">
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 text-forest text-[0.65rem] font-medium px-3 py-1 rounded-full">
                            Shop Now
                        </span>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-4">
                    <h3 class="font-semibold text-text text-sm mb-1 line-clamp-1 group-hover:text-forest transition-colors"
                        style="font-family: var(--font-display);">
                        <?= e($product['name']) ?>
                    </h3>
                    <p class="text-[0.7rem] text-muted leading-relaxed line-clamp-2 mb-3">
                        <?= e($product['description'] ?? '') ?>
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-forest font-bold text-sm">
                            ₱<?= number_format((float)$product['price'], 2) ?>
                        </span>
                        <span class="text-[0.65rem] text-forest font-medium group-hover:underline transition-all">
                            View →
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Mobile view all -->
        <div class="mt-8 text-center md:hidden">
            <a href="<?= APP_URL ?>/shop"
               class="inline-flex items-center gap-2 bg-forest hover:bg-pine text-white font-medium px-8 py-3 rounded-full text-sm transition-all shadow-sm">
                View all blooms
            </a>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- ── Why Petal & Soul ───────────────────────────────────────── -->
<section class="bg-cream border-t border-border py-16 px-6">
    <div class="max-w-5xl mx-auto text-center">

        <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-3">
            ✦ &nbsp; Why choose us &nbsp; ✦
        </p>
        <h2 class="text-3xl md:text-4xl text-forest mb-12"
            style="font-family: var(--font-display);">Blooms you can trust</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-6 border border-border shadow-sm">
                <div class="text-3xl mb-4">🌸</div>
                <h3 class="font-semibold text-text mb-2"
                    style="font-family: var(--font-display);">Fresh Daily</h3>
                <p class="text-sm text-muted leading-relaxed">
                    Every arrangement is handpicked and prepared fresh — never sitting on a shelf.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-border shadow-sm">
                <div class="text-3xl mb-4">🚚</div>
                <h3 class="font-semibold text-text mb-2"
                    style="font-family: var(--font-display);">Fast Delivery</h3>
                <p class="text-sm text-muted leading-relaxed">
                    Same-day delivery available. Your blooms arrive on time, every time.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-border shadow-sm">
                <div class="text-3xl mb-4">💳</div>
                <h3 class="font-semibold text-text mb-2"
                    style="font-family: var(--font-display);">Secure Payment</h3>
                <p class="text-sm text-muted leading-relaxed">
                    Pay online with confidence via PayMongo — your card details are never stored.
                </p>
            </div>
        </div>

    </div>
</section>