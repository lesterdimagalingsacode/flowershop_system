<?php
// app/views/auth/forgot-password.php
$title = 'Forgot Password';
?>
<div class="min-h-screen flex items-center justify-center py-16 px-4" style="background:#f0ebe1;">

    <div style="width:100%;max-width:440px;">

        <!-- Flourish -->
        <div class="flex items-center justify-center gap-3 mb-8">
            <div style="width:60px;height:1px;background:linear-gradient(to right,transparent,#c4973a);"></div>
            <span style="color:#c4973a;font-size:16px;">✦</span>
            <div style="width:60px;height:1px;background:linear-gradient(to left,transparent,#c4973a);"></div>
        </div>

        <!-- Card -->
        <div style="background:#ffffff;border-radius:20px;border:1px solid #e2d9cc;box-shadow:0 8px 40px rgba(0,0,0,0.07);overflow:hidden;">

            <!-- Header -->
            <div style="background:#1e3a2f;padding:36px 48px 32px;text-align:center;">
                <p style="margin:0 0 4px;color:#c4973a;font-size:10px;letter-spacing:4px;text-transform:uppercase;font-family:'Georgia',serif;">Petal &amp; Soul</p>
                <h1 style="margin:0 0 8px;color:#ffffff;font-size:22px;font-weight:normal;letter-spacing:2px;font-family:'Georgia',serif;">
                    Forgot Password?
                </h1>
                <p style="margin:0;color:rgba(255,255,255,0.5);font-size:12px;letter-spacing:1px;">
                    No worries — we'll send you a reset link
                </p>
            </div>

            <!-- Body -->
            <div style="padding:36px 40px 40px;">

                <?php if (Session::has('message')): ?>
                    <div style="background:#f0f9f4;border:1px solid #a8d5b5;border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#1e6640;font-size:13px;line-height:1.6;">
                        ✅ <?= htmlspecialchars(Session::flash('message')) ?>
                    </div>
                <?php endif; ?>

                <?php if (Session::has('error')): ?>
                    <div style="background:#fdf2f2;border:1px solid #f5c6cb;border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#8b1a2a;font-size:13px;line-height:1.6;">
                        <?= htmlspecialchars(Session::flash('error')) ?>
                    </div>
                <?php endif; ?>

                <p style="margin:0 0 24px;color:#7a6e63;font-size:14px;line-height:1.7;">
                    Enter the email address linked to your account and we'll send you a link to reset your password.
                </p>

                <form method="POST" action="<?= APP_URL ?>/forgot-password" novalidate>
                    <?= csrf_field() ?>

                    <!-- Email -->
                    <div style="margin-bottom:20px;">
                        <label style="display:block;color:#4a4035;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px;font-family:'Georgia',serif;">
                            Email Address
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            placeholder="you@example.com"
                            required
                            style="width:100%;box-sizing:border-box;padding:13px 16px;border:1px solid #e2d9cc;border-radius:10px;font-size:14px;color:#2c2420;background:#fdfaf7;outline:none;transition:border-color .2s;"
                            onfocus="this.style.borderColor='#1e3a2f'"
                            onblur="this.style.borderColor='#e2d9cc'"
                        >
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        style="width:100%;padding:14px;background:#1e3a2f;color:#ffffff;border:none;border-radius:50px;font-family:'Georgia',serif;font-size:13px;letter-spacing:2.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='#2d5443'"
                        onmouseout="this.style.background='#1e3a2f'"
                    >
                        Send Reset Link
                    </button>
                </form>

                <!-- Divider -->
                <div style="margin:24px 0;height:1px;background:linear-gradient(to right,transparent,#e2d9cc,transparent);"></div>

                <!-- Back to login -->
                <p style="text-align:center;margin:0;color:#9b9080;font-size:13px;">
                    Remember your password?
                    <a href="<?= APP_URL ?>/login" style="color:#1e3a2f;text-decoration:none;font-weight:normal;font-family:'Georgia',serif;">
                        Back to Login
                    </a>
                </p>

            </div>
        </div>

        <!-- Bottom flourish -->
        <p style="text-align:center;margin-top:24px;color:#c8bdb0;font-size:11px;letter-spacing:1px;">
            🌸 &nbsp; Flowers with Feeling &nbsp; 🌸
        </p>

    </div>
</div>