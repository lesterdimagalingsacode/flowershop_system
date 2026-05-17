<?php
// app/views/emails/reset-password.php
$appName = APP_NAME ?? 'Petal & Soul';
$appUrl  = APP_URL  ?? '#';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset your password — Petal &amp; Soul</title>
</head>
<body style="margin:0;padding:0;background:#f0ebe1;font-family:'Georgia',serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0ebe1;padding:48px 16px;">
    <tr>
        <td align="center">
            <table width="100%" style="max-width:540px;">

                <!-- Top flourish -->
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width:60px;height:1px;background:linear-gradient(to right,transparent,#c4973a);"></td>
                                <td style="padding:0 12px;color:#c4973a;font-size:18px;">✦</td>
                                <td style="width:60px;height:1px;background:linear-gradient(to left,transparent,#c4973a);"></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Main card -->
                <tr>
                    <td style="background:#ffffff;border-radius:20px;overflow:hidden;border:1px solid #e2d9cc;box-shadow:0 8px 40px rgba(0,0,0,0.07);">

                        <!-- Header -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background:#1e3a2f;padding:40px 48px 36px;text-align:center;position:relative;">
                                    <div style="position:absolute;top:16px;left:20px;width:6px;height:6px;border-radius:50%;background:rgba(196,151,58,0.4);"></div>
                                    <div style="position:absolute;top:28px;left:32px;width:4px;height:4px;border-radius:50%;background:rgba(196,151,58,0.25);"></div>
                                    <div style="position:absolute;top:16px;right:20px;width:6px;height:6px;border-radius:50%;background:rgba(196,151,58,0.4);"></div>
                                    <div style="position:absolute;top:28px;right:32px;width:4px;height:4px;border-radius:50%;background:rgba(196,151,58,0.25);"></div>

                                    <p style="margin:0 0 4px;color:#c4973a;font-size:11px;letter-spacing:4px;text-transform:uppercase;">Est. 2025</p>
                                    <h1 style="margin:0 0 12px;color:#ffffff;font-size:30px;font-weight:normal;letter-spacing:3px;font-family:'Georgia',serif;">
                                        Petal &amp; Soul
                                    </h1>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <table cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td style="width:32px;height:1px;background:rgba(196,151,58,0.4);"></td>
                                                        <td style="padding:0 8px;color:#c4973a;font-size:12px;">🔑</td>
                                                        <td style="width:32px;height:1px;background:rgba(196,151,58,0.4);"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:12px 0 0;color:rgba(255,255,255,0.55);font-size:12px;letter-spacing:1.5px;text-transform:uppercase;">
                                        Password Reset Request
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Body -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:44px 48px 40px;">

                                    <p style="margin:0 0 6px;color:#1e3a2f;font-size:24px;font-weight:normal;font-family:'Georgia',serif;">
                                        Hi <?= htmlspecialchars($name ?? 'there') ?> 🌿
                                    </p>
                                    <p style="margin:0 0 28px;color:#7a6e63;font-size:15px;line-height:1.7;">
                                        We received a request to reset the password for your Petal &amp; Soul account.
                                        Click the button below to choose a new password. This link expires in
                                        <strong style="color:#1e3a2f;">60 minutes</strong>.
                                    </p>

                                    <!-- Divider -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                        <tr>
                                            <td style="height:1px;background:linear-gradient(to right,transparent,#e2d9cc,transparent);"></td>
                                        </tr>
                                    </table>

                                    <!-- CTA Button -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                        <tr>
                                            <td align="center">
                                                <a href="<?= htmlspecialchars($link ?? '#') ?>"
                                                   style="display:inline-block;background:#1e3a2f;color:#ffffff;text-decoration:none;font-family:'Georgia',serif;font-size:13px;font-weight:normal;letter-spacing:2.5px;text-transform:uppercase;padding:16px 44px;border-radius:50px;border:1px solid #2d5443;">
                                                    Reset My Password
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Link fallback -->
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="background:#f7f3ee;border-radius:12px;border:1px solid #e8e0d4;margin-bottom:28px;">
                                        <tr>
                                            <td style="padding:16px 20px;">
                                                <p style="margin:0 0 6px;color:#9b9080;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;">
                                                    Or copy this link
                                                </p>
                                                <a href="<?= htmlspecialchars($link ?? '#') ?>"
                                                   style="color:#1e3a2f;font-size:12px;line-height:1.6;word-break:break-all;text-decoration:none;font-family:monospace;">
                                                    <?= htmlspecialchars($link ?? '') ?>
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Warning note -->
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="background:#fdf8f0;border-left:3px solid #c4973a;border-radius:0 10px 10px 0;margin-bottom:28px;">
                                        <tr>
                                            <td style="padding:12px 16px;">
                                                <p style="margin:0;color:#8a7355;font-size:12px;line-height:1.6;">
                                                    🔒 If you didn't request a password reset, you can safely ignore this email.
                                                    Your password will not change unless you click the link above.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="margin:0;color:#7a6e63;font-size:14px;line-height:1.7;">
                                        With love,<br>
                                        <span style="color:#1e3a2f;font-size:15px;">The Petal &amp; Soul Team 🌿</span>
                                    </p>

                                </td>
                            </tr>
                        </table>

                        <!-- Footer -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background:#f7f3ee;border-top:1px solid #e2d9cc;padding:20px 48px;text-align:center;">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
                                        <tr>
                                            <td align="center">
                                                <table cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td style="width:40px;height:1px;background:linear-gradient(to right,transparent,#c4973a);"></td>
                                                        <td style="padding:0 8px;color:#c4973a;font-size:10px;">✦</td>
                                                        <td style="width:40px;height:1px;background:linear-gradient(to left,transparent,#c4973a);"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:0 0 4px;color:#b0a898;font-size:12px;">
                                        &copy; <?= date('Y') ?> <?= htmlspecialchars($appName) ?> &nbsp;·&nbsp; Baler, Aurora, Philippines
                                    </p>
                                    <p style="margin:0;color:#c8bdb0;font-size:11px;">
                                        You received this because a password reset was requested for your account.
                                    </p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Bottom flourish -->
                <tr>
                    <td align="center" style="padding-top:24px;">
                        <p style="margin:0;color:#c8bdb0;font-size:11px;letter-spacing:1px;">
                            🌸 &nbsp; Flowers with Feeling &nbsp; 🌸
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>