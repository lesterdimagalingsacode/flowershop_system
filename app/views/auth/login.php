<?php
// app/views/auth/login.php
$title   = 'Sign In';
$oldData = $oldData ?? [];
$errors  = $errors  ?? [];
?>

<div class="min-h-screen bg-cream flex">

    <!-- Left decorative panel -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-ivory flex-col items-center justify-center p-16">

        <!-- Decorative background blobs -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 80%, #1e3a2f 0%, transparent 50%), radial-gradient(circle at 80% 20%, #c4973a 0%, transparent 50%);"></div>

        <!-- Decorative rings -->
        <div class="absolute top-16 right-16 w-48 h-48 rounded-full border border-border opacity-40"></div>
        <div class="absolute bottom-24 left-10 w-32 h-32 rounded-full border border-gold-lt opacity-50"></div>

        <!-- Brand mark -->
        <div class="relative text-center">
            <h2 class="text-5xl text-forest mb-3 tracking-wide" style="font-family: var(--font-display);">Petal & Soul</h2>
            <div class="w-16 h-px bg-gold mx-auto mb-6"></div>
            <p class="text-muted text-sm tracking-wider uppercase">Est. 2025 · Flowers with Feeling</p>
        </div>

        <!-- Tagline cards -->
        <div class="mt-16 space-y-4 w-full max-w-xs">
            <?php
            $quotes = [
                ['Every bouquet tells a story.'],
                ['Fresh blooms, lovingly curated.'],
                ['For family, friends & yourself.'],
            ];
            foreach ($quotes as [$text]):
            ?>
            <div class="flex items-center gap-3 bg-white/60 rounded-xl px-4 py-3 border border-border">
                <span class="text-text text-sm"><?= $text ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right — login form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-16">
        <div class="w-full max-w-md">

            <!-- Mobile logo -->
            <div class="lg:hidden text-center mb-10">
                <h1 class="text-4xl text-forest tracking-wide" style="font-family: var(--font-display);">Petal & Soul</h1>
                <div class="w-12 h-px bg-gold mx-auto mt-2"></div>
            </div>

            <!-- Heading -->
            <div class="mb-8">
                <h1 class="text-4xl text-text mb-2" style="font-family: var(--font-display);">Welcome back</h1>
                <p class="text-muted text-sm">Sign in to your account to continue</p>
            </div>

            <!-- Flash error -->
            <?php if (!empty($errors) && isset($errors[0])): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <?= e($errors[0]) ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="<?= APP_URL ?>/login" class="space-y-5">
                <?= csrf_field() ?>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= e($oldData['email'] ?? '') ?>"
                        placeholder="you@example.com"
                        class="w-full bg-white border <?= !empty($errors['email']) ? 'border-red-400' : 'border-border' ?> rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                        autocomplete="email"
                        required
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <p class="mt-1 text-xs text-red-500"><?= e($errors['email'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-medium text-text tracking-widest uppercase">
                            Password
                        </label>
                        <a href="<?= APP_URL ?>/forgot-password" class="text-xs text-forest hover:text-pine transition">
                            Forgot password?
                        </a>
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            class="w-full bg-white border <?= !empty($errors['password']) ? 'border-red-400' : 'border-border' ?> rounded-xl px-4 py-3 pr-12 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                            autocomplete="current-password"
                            required
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-text transition"
                            tabindex="-1"
                            aria-label="Show password"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errors['password'])): ?>
                        <p class="mt-1 text-xs text-red-500"><?= e($errors['password'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Remember me -->
                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="w-4 h-4 rounded border-border focus:ring-forest/30 accent-forest"
                    >
                    <label for="remember" class="text-sm text-muted select-none cursor-pointer">
                        Keep me signed in
                    </label>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-forest text-white font-medium text-sm tracking-widest uppercase rounded-xl px-6 py-3.5 hover:bg-pine transition-colors duration-200 mt-2"
                >
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-4 my-7">
                <div class="flex-1 h-px bg-border"></div>
                <span class="text-xs text-muted tracking-wider">or</span>
                <div class="flex-1 h-px bg-border"></div>
            </div>

            <!-- Register link -->
            <p class="text-center text-sm text-muted">
                Don't have an account?
                <a href="<?= APP_URL ?>/register" class="text-forest hover:text-pine font-medium transition ml-1">
                    Create one
                </a>
            </p>

            <!-- Back to home -->
            <div class="mt-8 text-center">
                <a href="<?= APP_URL ?>/" class="inline-flex items-center gap-2 text-xs text-muted hover:text-text transition tracking-wider">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Petal & Soul
                </a>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
}
</script>