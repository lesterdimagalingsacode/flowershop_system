<?php
$title = 'Login';
$old    = Session::getFlash('old')['message']    ?? [];
$old    = Session::get('_flash')['old']['message'] ?? [];
$old    = $_SESSION['_flash']['old']['message']  ?? [];
// Simpler: just read old from flash
$oldData = Session::getFlash('old') ?? [];
$errors  = Session::getFlash('errors') ?? [];
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="text-4xl mb-3">🌸</div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome back</h1>
                <p class="text-gray-500 text-sm mt-1">Sign in to your account</p>
            </div>

            <!-- Form -->
            <form method="POST" action="<?= APP_URL ?>/login" id="loginForm" novalidate>
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email address
                    </label>
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

                <!-- Password -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['password']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition pr-10"
                        >
                        <!-- Show/hide toggle -->
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
                            👁
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['password'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm shadow-sm">
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400">or</span>
                <div class="flex-1 h-px bg-gray-100"></div>
            </div>

            <!-- Register link -->
            <p class="text-center text-sm text-gray-500">
                Don't have an account?
                <a href="/register" class="text-rose-500 font-medium hover:underline">Sign up</a>
            </p>
        </div>

        <!-- Back to shop -->
        <p class="text-center text-xs text-gray-400 mt-4">
            <a href="/shop" class="hover:text-rose-500 transition-colors">← Continue browsing</a>
        </p>
    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Frontend validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    if (!email || !password) {
        e.preventDefault();
        alert('Please fill in all fields.');
    }
});
</script>
