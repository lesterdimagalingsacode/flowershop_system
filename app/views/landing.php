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

        <!-- LEFT — Tulips, moved up, no card wrapper -->
        <div class="hidden md:block w-72 lg:w-80 flex-shrink-0 -translate-y-8">
            <img src="<?= APP_URL ?>/images/tulips.png"
                 alt="Tulips"
                 class="w-full object-contain mix-blend-multiply">
        </div>

        <!-- CENTER — Bouquet, tallest, overlaps headline -->
        <div class="relative w-64 md:w-80 lg:w-96 rounded-3xl overflow-hidden shadow-2xl flex-shrink-0 -translate-y-4"
             style="box-shadow: 0 40px 80px rgba(30,58,47,0.3);">
            <img src="<?= APP_URL ?>/images/Boquet.png"
                 alt="Bouquet"
                 class="w-full object-cover z-50">

            <!-- Badge overlaid on bouquet bottom -->
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

            <!-- Small floating badge -->
            <div class="bg-white rounded-2xl px-4 py-3 shadow-lg flex items-center gap-3 border border-border">
                <span class="text-xl">💛</span>
                <div>
                    <p class="text-xs font-semibold text-text">Made with love</p>
                    <p class="text-[0.65rem] text-muted">Every arrangement</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Mobile CTA (shown only on small screens) -->
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