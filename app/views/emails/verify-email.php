<?php
// app/views/emails/verify-email.php
$appName = APP_NAME ?? 'Petal & Soul';
$appUrl  = APP_URL  ?? '#';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email</title>
</head>
<body style="margin:0;padding:0;background:#f5f0e8;font-family:'Georgia',serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f0e8;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width:520px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e8e0d0;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#1e3a2f;padding:32px 40px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:normal;letter-spacing:2px;">
                                Petal &amp; Soul
                            </h1>
                            <div style="width:40px;height:1px;background:#c4973a;margin:12px auto 0;"></div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px 40px 32px;">
                            <p style="margin:0 0 8px;color:#2d2d2d;font-size:22px;">Hi <?= htmlspecialchars($name ?? 'there') ?> 🌸</p>
                            <p style="margin:0 0 24px;color:#6b6b6b;font-size:15px;line-height:1.6;">
                                Thanks for creating your account! Please verify your email address to complete your registration.
                            </p>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding:8px 0 32px;">
                                        <a href="<?= htmlspecialchars($link ?? '#') ?>"
                                           style="display:inline-block;background:#1e3a2f;color:#ffffff;text-decoration:none;font-size:14px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;padding:14px 36px;border-radius:10px;">
                                            Verify My Email
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px;color:#9b9b9b;font-size:13px;line-height:1.6;">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:0 0 24px;word-break:break-all;">
                                <a href="<?= htmlspecialchars($link ?? '#') ?>"
                                   style="color:#1e3a2f;font-size:13px;">
                                    <?= htmlspecialchars($link ?? '') ?>
                                </a>
                            </p>

                            <p style="margin:0;color:#b0a898;font-size:12px;line-height:1.6;">
                                If you didn't create an account, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f5f0e8;padding:20px 40px;text-align:center;border-top:1px solid #e8e0d0;">
                            <p style="margin:0;color:#b0a898;font-size:12px;">
                                &copy; <?= date('Y') ?> <?= htmlspecialchars($appName) ?> · Flowers with Feeling
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>