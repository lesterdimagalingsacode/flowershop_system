<?php
// app/views/emails/inquiry-reply.php
// Used by Mailer when admin replies to a customer inquiry
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reply from Petal &amp; Soul</title>
    <style>
        body        { margin: 0; padding: 0; background: #f9fafb; font-family: 'Segoe UI', Arial, sans-serif; color: #374151; }
        .wrapper    { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header     { background: #ec4899; padding: 28px 32px; text-align: center; }
        .header h1  { margin: 0; color: #fff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
        .header p   { margin: 4px 0 0; color: #fce7f3; font-size: 13px; }
        .body       { padding: 32px; }
        .greeting   { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
        .body p     { font-size: 14px; line-height: 1.6; margin: 0 0 16px; color: #4b5563; }
        .quote-box  { background: #fdf2f8; border-left: 3px solid #f9a8d4; border-radius: 0 8px 8px 0; padding: 12px 16px; margin: 20px 0; }
        .quote-box p { margin: 0; font-size: 13px; color: #6b7280; font-style: italic; }
        .reply-box  { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px 20px; margin: 20px 0; }
        .reply-box p { margin: 0; font-size: 14px; color: #166534; white-space: pre-wrap; }
        .footer     { padding: 20px 32px; border-top: 1px solid #f3f4f6; text-align: center; }
        .footer p   { font-size: 12px; color: #9ca3af; margin: 0; }
        .footer a   { color: #ec4899; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>🌸 Petal &amp; Soul</h1>
            <p>We've replied to your inquiry</p>
        </div>
        <div class="body">
            <p class="greeting">Hi <?= htmlspecialchars($customerName) ?>,</p>
            <p>Thank you for reaching out! Our team has responded to your message.</p>

            <p style="font-size:12px;text-transform:uppercase;font-weight:600;color:#9ca3af;letter-spacing:.05em;margin-bottom:4px;">Your original message</p>
            <div class="quote-box">
                <p><?= nl2br(htmlspecialchars($originalMsg)) ?></p>
            </div>

            <p style="font-size:12px;text-transform:uppercase;font-weight:600;color:#9ca3af;letter-spacing:.05em;margin-bottom:4px;">Our reply</p>
            <div class="reply-box">
                <p><?= nl2br(htmlspecialchars($replyBody)) ?></p>
            </div>

            <p>If you have further questions, feel free to reply to this email or <a href="<?= APP_URL ?>/contact" style="color:#ec4899;">contact us again</a>.</p>
            <p>With love,<br/><strong>The Petal &amp; Soul Team</strong></p>
        </div>
        <div class="footer">
            <p>Baler, Aurora, Philippines &nbsp;·&nbsp; <a href="<?= APP_URL ?>">petalsoul.com</a></p>
            <p style="margin-top:6px;">You received this because you submitted an inquiry on our website.</p>
        </div>
    </div>
</body>
</html>