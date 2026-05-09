<?php
$title   = 'Create Account';
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
                ['🌿', 'Join a community of flower lovers.'],
                ['🌸', 'Fresh blooms for every occasion.'],
                ['🎁', 'Send love to family & friends.'],
            ];
            foreach ($quotes as [$icon, $text]):
            ?>
            <div class="flex items-center gap-3 bg-white/60 rounded-xl px-4 py-3 border border-border">
                <span class="text-lg"><?= $icon ?></span>
                <span class="text-text text-sm"><?= $text ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right — register form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            <!-- Mobile logo -->
            <div class="lg:hidden text-center mb-10">
                <h1 class="text-4xl text-forest tracking-wide" style="font-family: var(--font-display);">Petal & Soul</h1>
                <div class="w-12 h-px bg-gold mx-auto mt-2"></div>
            </div>

            <!-- Heading -->
            <div class="mb-8">
                <h1 class="text-4xl text-text mb-2" style="font-family: var(--font-display);">Create account</h1>
                <p class="text-muted text-sm">Join us and start sending beautiful blooms</p>
            </div>

            <!-- Flash errors -->
            <?php if (!empty($errors) && isset($errors[0])): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <?= e($errors[0]) ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="<?= APP_URL ?>/register" id="registerForm" class="space-y-4" novalidate>
                <?= csrf_field() ?>

                <!-- First name + Last name -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="first_name" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                            First Name
                        </label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="<?= e($oldData['first_name'] ?? '') ?>"
                            placeholder="Juan"
                            autocomplete="given-name"
                            class="w-full bg-white border <?= !empty($errors['first_name']) ? 'border-red-400' : 'border-border' ?> rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                            required
                        >
                        <?php if (!empty($errors['first_name'])): ?>
                            <p class="mt-1 text-xs text-red-500"><?= e($errors['first_name'][0]) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                            Last Name
                        </label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?= e($oldData['last_name'] ?? '') ?>"
                            placeholder="Dela Cruz"
                            autocomplete="family-name"
                            class="w-full bg-white border <?= !empty($errors['last_name']) ? 'border-red-400' : 'border-border' ?> rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                            required
                        >
                        <?php if (!empty($errors['last_name'])): ?>
                            <p class="mt-1 text-xs text-red-500"><?= e($errors['last_name'][0]) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Middle name -->
                <div>
                    <label for="middle_name" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                        Middle Name <span class="text-muted normal-case tracking-normal">(optional)</span>
                    </label>
                    <input
                        type="text"
                        id="middle_name"
                        name="middle_name"
                        value="<?= e($oldData['middle_name'] ?? '') ?>"
                        placeholder="Santos"
                        autocomplete="additional-name"
                        class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                    >
                </div>

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
                        autocomplete="email"
                        class="w-full bg-white border <?= !empty($errors['email']) ? 'border-red-400' : 'border-border' ?> rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                        required
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <p class="mt-1 text-xs text-red-500"><?= e($errors['email'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                        Phone <span class="text-muted normal-case tracking-normal">(optional)</span>
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="<?= e($oldData['phone'] ?? '') ?>"
                        placeholder="09XX XXX XXXX"
                        autocomplete="tel"
                        class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Min. 8 characters"
                            autocomplete="new-password"
                            class="w-full bg-white border <?= !empty($errors['password']) ? 'border-red-400' : 'border-border' ?> rounded-xl px-4 py-3 pr-12 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                            required
                        >
                        <button type="button"
                            onclick="togglePassword('password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-text transition"
                            tabindex="-1"
                            aria-label="Show password">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errors['password'])): ?>
                        <p class="mt-1 text-xs text-red-500"><?= e($errors['password'][0]) ?></p>
                    <?php endif; ?>

                    <!-- Strength bars -->
                    <div class="flex gap-1 mt-2" id="strengthBars">
                        <div class="h-1 flex-1 rounded-full bg-border" id="bar1"></div>
                        <div class="h-1 flex-1 rounded-full bg-border" id="bar2"></div>
                        <div class="h-1 flex-1 rounded-full bg-border" id="bar3"></div>
                        <div class="h-1 flex-1 rounded-full bg-border" id="bar4"></div>
                    </div>
                    <p id="strengthLabel" class="text-xs mt-1 hidden"></p>

                    <!-- Requirements checklist -->
                    <ul id="pwChecklist" class="mt-2 space-y-1 hidden">
                        <li id="req-length"  class="flex items-center gap-2 text-xs text-muted">
                            <span class="req-icon w-3 text-center">○</span> At least 8 characters
                        </li>
                        <li id="req-upper"   class="flex items-center gap-2 text-xs text-muted">
                            <span class="req-icon w-3 text-center">○</span> One uppercase letter (A–Z)
                        </li>
                        <li id="req-number"  class="flex items-center gap-2 text-xs text-muted">
                            <span class="req-icon w-3 text-center">○</span> One number (0–9)
                        </li>
                        <li id="req-special" class="flex items-center gap-2 text-xs text-muted">
                            <span class="req-icon w-3 text-center">○</span> One special character (!@#$…)
                        </li>
                    </ul>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Re-enter password"
                            autocomplete="new-password"
                            class="w-full bg-white border <?= !empty($errors['password_confirmation']) ? 'border-red-400' : 'border-border' ?> rounded-xl px-4 py-3 pr-12 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                            required
                        >
                        <button type="button"
                            onclick="togglePassword('password_confirmation', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-text transition"
                            tabindex="-1"
                            aria-label="Show password">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errors['password_confirmation'])): ?>
                        <p class="mt-1 text-xs text-red-500"><?= e($errors['password_confirmation'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-forest text-white font-medium text-sm tracking-widest uppercase rounded-xl px-6 py-3.5 hover:bg-pine transition-colors duration-200 mt-2"
                >
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-4 my-7">
                <div class="flex-1 h-px bg-border"></div>
                <span class="text-xs text-muted tracking-wider">or</span>
                <div class="flex-1 h-px bg-border"></div>
            </div>

            <!-- Login link -->
            <p class="text-center text-sm text-muted">
                Already have an account?
                <a href="<?= APP_URL ?>/login" class="text-forest hover:text-pine font-medium transition ml-1">
                    Sign in
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

// Password strength + checklist
document.getElementById('password').addEventListener('input', function () {
    const val      = this.value;
    const bars     = [bar1, bar2, bar3, bar4];
    const colors   = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
    const labels   = ['Weak', 'Fair', 'Good', 'Strong'];
    const labelColors = ['#f87171', '#fb923c', '#facc15', '#22c55e'];

    const checks = {
        length:  val.length >= 8,
        upper:   /[A-Z]/.test(val),
        number:  /[0-9]/.test(val),
        special: /[^A-Za-z0-9]/.test(val),
    };

    const strength = Object.values(checks).filter(Boolean).length;

    // Update bars
    bars.forEach((bar, i) => {
        bar.className = 'h-1 flex-1 rounded-full ' +
            (i < strength ? colors[strength - 1] : 'bg-border');
    });

    // Update strength label
    const label = document.getElementById('strengthLabel');
    if (val.length > 0) {
        label.style.color = labelColors[strength - 1] ?? '#9ca3af';
        label.textContent  = (labels[strength - 1] ?? 'Weak') + ' password';
        label.classList.remove('hidden');
    } else {
        label.classList.add('hidden');
    }

    // Show/hide checklist
    const checklist = document.getElementById('pwChecklist');
    checklist.classList.toggle('hidden', val.length === 0);

    // Update each requirement row
    const reqMap = {
        'req-length':  checks.length,
        'req-upper':   checks.upper,
        'req-number':  checks.number,
        'req-special': checks.special,
    };

    Object.entries(reqMap).forEach(([id, passed]) => {
        const li   = document.getElementById(id);
        const icon = li.querySelector('.req-icon');
        if (passed) {
            li.style.color = '#16a34a';   // green-600
            icon.textContent = '✓';
        } else {
            li.style.color = '';
            li.classList.add('text-muted');
            icon.textContent = '○';
        }
    });
});

// Frontend validation
document.getElementById('registerForm').addEventListener('submit', function (e) {
    const password = document.getElementById('password').value;
    const confirm  = document.getElementById('password_confirmation').value;
    if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match.');
        return;
    }
    if (password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters.');
    }
});
</script>