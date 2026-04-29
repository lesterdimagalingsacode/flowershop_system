<?php $title = 'Welcome'; ?>

<style>
    @keyframes float1 { 0%,100%{transform:translateY(0) rotate(-20deg);} 50%{transform:translateY(-18px) rotate(-20deg);} }
    @keyframes float2 { 0%,100%{transform:translateY(0) rotate(15deg);}  50%{transform:translateY(-12px) rotate(15deg);} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(24px);} to{opacity:1;transform:translateY(0);} }
    .fade-up-1 { animation: fadeUp 0.8s 0.0s ease both; }
    .fade-up-2 { animation: fadeUp 0.8s 0.15s ease both; }
    .fade-up-3 { animation: fadeUp 0.8s 0.3s ease both; }
    .fade-up-4 { animation: fadeUp 0.8s 0.45s ease both; }
    .petal-float-1 { animation: float1 8s ease-in-out infinite; }
    .petal-float-2 { animation: float2 10s ease-in-out infinite; }
    .petal-float-3 { animation: float1 9s ease-in-out infinite reverse; }
    .petal-float-4 { animation: float2 7s ease-in-out infinite; }
</style>

<!-- ── Hero ── -->
<section class="relative min-h-[92vh] flex items-center justify-center overflow-hidden px-6 py-24">

    <!-- Background glows -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_40%,rgba(232,96,122,0.12),transparent_70%)] pointer-events-none"></div>
    <div class="absolute -top-[10%] -left-[10%] w-1/2 h-[60%] bg-[radial-gradient(circle,rgba(201,169,110,0.06),transparent_60%)] pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-2/5 h-1/2 bg-[radial-gradient(circle,rgba(232,96,122,0.07),transparent_60%)] pointer-events-none"></div>

    <!-- Floating petals -->
    <span class="absolute top-[12%] left-[8%] text-5xl opacity-[0.08] petal-float-1 pointer-events-none">🌸</span>
    <span class="absolute top-[20%] right-[10%] text-4xl opacity-[0.07] petal-float-2 pointer-events-none">🌺</span>
    <span class="absolute bottom-[20%] left-[12%] text-3xl opacity-[0.07] petal-float-3 pointer-events-none">🌹</span>
    <span class="absolute bottom-[15%] right-[8%] text-5xl opacity-[0.06] petal-float-4 pointer-events-none">🌷</span>

    <div class="relative z-10 text-center max-w-2xl mx-auto">

        <!-- Eyebrow -->
        <p class="fade-up-1 text-xs tracking-[0.2em] uppercase text-gold mb-6 font-medium">
            ✦ &nbsp; Flowers for every soul &nbsp; ✦
        </p>

        <!-- Headline -->
        <h1 class="fade-up-2 font-(--font-display) text-[clamp(3rem,7vw,5.5rem)] font-normal leading-[1.1] text-petal mb-6">
            Blooms that speak<br>
            <em class="text-rose not-italic">from the heart</em>
        </h1>

        <!-- Sub -->
        <p class="fade-up-3 text-[1.05rem] text-muted leading-relaxed max-w-md mx-auto mb-10">
            Whether for a loved one, a friend, or yourself — every arrangement is crafted with intention, beauty, and soul.
        </p>

        <!-- CTAs -->
        <div class="fade-up-4 flex gap-4 justify-center flex-wrap">
            <a href="<?= APP_URL ?>/shop"
               class="bg-rose hover:bg-rose/85 text-white font-semibold px-8 py-3.5 rounded-full text-[0.95rem] transition-all hover:-translate-y-px">
                Explore the Shop
            </a>
            <a href="<?= APP_URL ?>/register"
               class="border border-subtle text-petal hover:border-petal hover:text-white font-medium px-8 py-3.5 rounded-full text-[0.95rem] transition-all">
                Create an Account
            </a>
        </div>

        <!-- Trust -->
        <p class="mt-10 text-xs text-subtle tracking-wide">
            🌸 &nbsp; Fresh arrangements &nbsp;·&nbsp; Secure checkout &nbsp;·&nbsp; Made with love
        </p>
    </div>
</section>

<!-- ── Divider ── -->
<div class="h-px bg-gradient-to-r from-transparent via-border to-transparent mx-8"></div>

<!-- ── Occasions ── -->
<section class="px-6 py-20">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-14">
            <p class="text-xs tracking-[0.18em] uppercase text-gold mb-3 font-medium">For every moment</p>
            <h2 class="font-(--font-display) text-[clamp(2rem,4vw,3rem)] font-normal text-petal">Flowers for all occasions</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php
            $occasions = [
                ['🌹', 'Romance',     'For someone who makes your heart bloom.'],
                ['🌸', 'Family',      'Celebrate the ones who shape your world.'],
                ['💐', 'Friendship',  'Because good friends deserve beautiful things.'],
                ['🌻', 'Celebration', 'Birthdays, milestones, and new beginnings.'],
                ['🕊️', 'Sympathy',    'A gentle gesture when words fall short.'],
                ['🌷', 'Self-care',   'You deserve your own flowers too.'],
            ];
            foreach ($occasions as $o): ?>
            <div class="bg-card border border-border rounded-2xl p-6 text-center hover:border-rose/40 hover:-translate-y-1 transition-all duration-200 cursor-default">
                <div class="text-3xl mb-3"><?= $o[0] ?></div>
                <h3 class="font-(--font-display) text-[1.05rem] font-medium text-petal mb-2"><?= $o[1] ?></h3>
                <p class="text-xs text-muted leading-relaxed"><?= $o[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Divider ── -->
<div class="h-px bg-gradient-to-r from-transparent via-border to-transparent mx-8"></div>

<!-- ── Featured Products ── -->
<section class="px-6 py-20">
    <div class="max-w-6xl mx-auto">

        <div class="flex justify-between items-end mb-12 flex-wrap gap-4">
            <div>
                <p class="text-xs tracking-[0.18em] uppercase text-gold mb-2 font-medium">Handpicked</p>
                <h2 class="font-(--font-display) text-[clamp(2rem,4vw,3rem)] font-normal text-petal">Featured arrangements</h2>
            </div>
            <a href="<?= APP_URL ?>/shop" class="text-sm text-rose hover:text-petal font-medium transition-colors flex items-center gap-1">
                View all &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php
            $featured = [
                ['🌹', 'Crimson Reverie',  'A bold arrangement of deep red roses.',   '₱850'],
                ['💐', 'Pastel Whisper',   'Soft pinks and whites, gently arranged.',  '₱720'],
                ['🌺', 'Wild Bloom',       'An untamed mix of seasonal blooms.',       '₱650'],
                ['🌷', 'Tender Grace',     'Tulips and lilies in a soft palette.',     '₱780'],
            ];
            foreach ($featured as $p): ?>
            <div class="bg-card border border-border rounded-2xl overflow-hidden hover:border-rose/40 hover:-translate-y-1 transition-all duration-200">
                <div class="bg-surface h-44 flex items-center justify-center text-6xl border-b border-border">
                    <?= $p[0] ?>
                </div>
                <div class="p-5">
                    <h3 class="font-(--font-display) text-[1.05rem] font-medium text-petal mb-1"><?= $p[1] ?></h3>
                    <p class="text-xs text-muted mb-4 leading-relaxed"><?= $p[2] ?></p>
                    <div class="flex justify-between items-center">
                        <span class="text-rose font-semibold"><?= $p[3] ?></span>
                        <a href="<?= APP_URL ?>/shop"
                           class="bg-rose hover:bg-rose/85 text-white text-xs font-semibold px-4 py-1.5 rounded-full transition-colors">
                            Shop Now
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Divider ── -->
<div class="h-px bg-gradient-to-r from-transparent via-border to-transparent mx-8"></div>

<!-- ── Why Us ── -->
<section class="px-6 py-20">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-14">
            <p class="text-xs tracking-[0.18em] uppercase text-gold mb-3 font-medium">Why Petal & Soul</p>
            <h2 class="font-(--font-display) text-[clamp(2rem,4vw,3rem)] font-normal text-petal">Crafted with intention</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <?php
            $whys = [
                ['🌿', 'Fresh Daily',       'Every arrangement is prepared fresh the same day.'],
                ['💳', 'Secure Payments',   'Powered by PayMongo — safe and seamless checkout.'],
                ['📦', 'Careful Packaging', 'Your blooms arrive exactly as they left our hands.'],
                ['💌', 'From the Heart',    'Each order carries a piece of someone\'s story.'],
            ];
            foreach ($whys as $w): ?>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-surface border border-border flex items-center justify-center text-2xl">
                    <?= $w[0] ?>
                </div>
                <h3 class="font-(--font-display) text-[1.05rem] font-medium text-petal"><?= $w[1] ?></h3>
                <p class="text-xs text-muted leading-relaxed max-w-[180px]"><?= $w[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── CTA Banner ── -->
<section class="px-6 py-20">
    <div class="max-w-6xl mx-auto">
        <div class="relative bg-gradient-to-br from-card via-surface to-card border border-border rounded-3xl px-8 py-20 text-center overflow-hidden">

            <!-- Glow -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_60%_80%_at_50%_50%,rgba(232,96,122,0.1),transparent_70%)] pointer-events-none"></div>

            <p class="relative text-xs tracking-[0.18em] uppercase text-gold mb-4 font-medium">Start your story</p>
            <h2 class="relative font-(--font-display) text-[clamp(2rem,5vw,3.5rem)] font-normal text-petal mb-5">
                Someone is waiting<br>for your flowers.
            </h2>
            <p class="relative text-[0.95rem] text-muted mb-8 max-w-md mx-auto leading-relaxed">
                Create your account today and send something beautiful.
            </p>
            <div class="relative flex gap-4 justify-center flex-wrap">
                <a href="<?= APP_URL ?>/register"
                   class="bg-rose hover:bg-rose/85 text-white font-semibold px-9 py-3.5 rounded-full text-[1rem] transition-all hover:-translate-y-px">
                    Get Started — It's Free
                </a>
                <a href="<?= APP_URL ?>/shop"
                   class="border border-subtle text-petal hover:border-petal hover:text-white font-medium px-9 py-3.5 rounded-full text-[1rem] transition-all">
                    Browse the Shop
                </a>
            </div>
        </div>
    </div>
</section>