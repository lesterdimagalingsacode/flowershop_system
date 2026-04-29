<?php
$title   = 'Create Account';
$oldData = Session::getFlash('old')    ?? [];
$errors  = Session::getFlash('errors') ?? [];
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="text-4xl mb-3">🌺</div>
                <h1 class="text-2xl font-bold text-gray-800">Create your account</h1>
                <p class="text-gray-500 text-sm mt-1">Join us and start ordering flowers</p>
            </div>

            <!-- Form -->
            <form method="POST" action="/register" id="registerForm" novalidate>
                <?= csrf_field() ?>

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= e($oldData['name'] ?? '') ?>"
                        placeholder="Juan Dela Cruz"
                        autocomplete="name"
                        class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['name']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition"
                    >
                    <?php if (isset($errors['name'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['name'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= e($oldData['email'] ?? '') ?>"
                        placeholder="you@example.com"
                        autocomplete="email"
                        class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition"
                    >
                    <?php if (isset($errors['email'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['email'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Phone (optional) -->
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Phone number <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="<?= e($oldData['phone'] ?? '') ?>"
                        placeholder="09XX XXX XXXX"
                        autocomplete="tel"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition"
                    >
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Min. 8 characters"
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['password']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition pr-10"
                        >
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">👁</button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['password'][0]) ?></p>
                    <?php endif; ?>
                    <!-- Strength indicator -->
                    <div class="flex gap-1 mt-2" id="strengthBars">
                        <div class="h-1 flex-1 rounded-full bg-gray-200" id="bar1"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200" id="bar2"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200" id="bar3"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200" id="bar4"></div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Confirm password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Re-enter password"
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['password_confirmation']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition pr-10"
                        >
                        <button type="button" onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">👁</button>
                    </div>
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['password_confirmation'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm shadow-sm">
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400">or</span>
                <div class="flex-1 h-px bg-gray-100"></div>
            </div>

            <!-- Login link -->
            <p class="text-center text-sm text-gray-500">
                Already have an account?
                <a href="/login" class="text-rose-500 font-medium hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const val    = this.value;
    const bars   = [bar1, bar2, bar3, bar4];
    const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-400'];
    let strength = 0;

    if (val.length >= 8)              strength++;
    if (/[A-Z]/.test(val))            strength++;
    if (/[0-9]/.test(val))            strength++;
    if (/[^A-Za-z0-9]/.test(val))     strength++;

    bars.forEach((bar, i) => {
        bar.className = 'h-1 flex-1 rounded-full ' +
            (i < strength ? colors[strength - 1] : 'bg-gray-200');
    });
});

// Frontend validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
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
