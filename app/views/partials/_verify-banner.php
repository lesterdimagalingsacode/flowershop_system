<?php
// app/views/partials/_verify-banner.php
// Include this in your main layout, right after <body> or top of content area.
// Only shows when logged in and email is not verified.

if (!Session::isLoggedIn()) return;

$userModel = new User();
if ($userModel->isEmailVerified(Session::userId())) return;
?>

<div id="verifyBanner" class="bg-amber-50 border-b border-amber-200 px-4 py-3">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-2 text-sm text-amber-800">
            <svg class="w-4 h-4 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>Please verify your email address to fully activate your account.</span>
        </div>
        <div class="flex items-center gap-3">
            <span id="bannerMsg" class="text-xs text-amber-700 hidden"></span>
            <button
                id="resendBtn"
                onclick="resendVerification()"
                class="text-xs font-semibold text-amber-800 underline hover:text-amber-900 transition whitespace-nowrap">
                Resend verification email
            </button>
        </div>
    </div>
</div>

<script>
async function resendVerification() {
    const btn = document.getElementById('resendBtn');
    const msg = document.getElementById('bannerMsg');

    btn.disabled    = true;
    btn.textContent = 'Sending…';

    try {
        const res  = await fetch('<?= APP_URL ?>/resend-verification', {
            method : 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body   : '<?= CSRF_TOKEN_NAME ?>=<?= csrf_token() ?>',
        });
        const data = await res.json();

        msg.textContent = data.message ?? (data.success ? 'Email sent!' : 'Failed. Try again.');
        msg.classList.remove('hidden');

        if (data.success) {
            btn.textContent = 'Email sent ✓';
        } else {
            btn.disabled    = false;
            btn.textContent = 'Resend verification email';
        }
    } catch {
        msg.textContent = 'Network error. Try again.';
        msg.classList.remove('hidden');
        btn.disabled    = false;
        btn.textContent = 'Resend verification email';
    }
}
</script>