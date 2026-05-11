<?php
// app/views/emails/order-confirmation.php
// Variables available: $order, $items, $customer
// Called from OrderController::checkout() after order is placed

$appName = APP_NAME ?? 'Petal & Soul';
$appUrl  = APP_URL  ?? '#';

$paymentLabel = match($order['payment_method'] ?? 'cod') {
    'online' => '💳 Online Payment',
    'cod'    => '💵 Cash on Delivery',
    default  => ucfirst($order['payment_method'] ?? 'COD'),
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Order Confirmed — <?= htmlspecialchars($appName) ?></title>
</head>
<body style="margin:0;padding:0;background:#f0ebe1;font-family:'Georgia',serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0ebe1;padding:48px 16px;">
    <tr>
        <td align="center">
            <table width="100%" style="max-width:560px;">

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
                                <td style="background:#1e3a2f;padding:36px 48px 32px;text-align:center;">
                                    <p style="margin:0 0 4px;color:#c4973a;font-size:11px;letter-spacing:4px;text-transform:uppercase;">Est. 2025</p>
                                    <h1 style="margin:0 0 12px;color:#ffffff;font-size:28px;font-weight:normal;letter-spacing:3px;font-family:'Georgia',serif;">
                                        Petal &amp; Soul
                                    </h1>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <table cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td style="width:32px;height:1px;background:rgba(196,151,58,0.4);"></td>
                                                        <td style="padding:0 8px;color:#c4973a;font-size:12px;">🌸</td>
                                                        <td style="width:32px;height:1px;background:rgba(196,151,58,0.4);"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:12px 0 0;color:rgba(255,255,255,0.55);font-size:12px;letter-spacing:1.5px;text-transform:uppercase;">
                                        Order Confirmation
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Order number badge -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:32px 48px 0;">
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="background:#f7f3ee;border:1px solid #e2d9cc;border-radius:16px;">
                                        <tr>
                                            <td style="padding:20px 24px;text-align:center;">
                                                <p style="margin:0 0 4px;color:#9b9080;font-size:11px;letter-spacing:3px;text-transform:uppercase;">Order Number</p>
                                                <p style="margin:0 0 8px;color:#1e3a2f;font-size:26px;font-weight:normal;font-family:'Georgia',serif;letter-spacing:1px;">
                                                    <?= htmlspecialchars($order['order_number'] ?? '#' . $order['id']) ?>
                                                </p>
                                                <span style="display:inline-block;background:#1e3a2f;color:#c4973a;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;padding:5px 16px;border-radius:50px;">
                                                    <?= $paymentLabel ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Body -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:28px 48px 40px;">

                                    <!-- Greeting -->
                                    <p style="margin:0 0 6px;color:#1e3a2f;font-size:20px;font-weight:normal;font-family:'Georgia',serif;">
                                        Hi <?= htmlspecialchars($customer['name'] ?? 'there') ?> 🌸
                                    </p>
                                    <p style="margin:0 0 24px;color:#7a6e63;font-size:14px;line-height:1.7;">
                                        Thank you for your order! We've received it and will start preparing your
                                        beautiful blooms shortly. Here's your order summary:
                                    </p>

                                    <!-- Divider -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                                        <tr>
                                            <td style="height:1px;background:linear-gradient(to right,transparent,#e2d9cc,transparent);"></td>
                                        </tr>
                                    </table>

                                    <!-- Items -->
                                    <p style="margin:0 0 12px;color:#9b9080;font-size:11px;letter-spacing:2px;text-transform:uppercase;">Items Ordered</p>
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:4px;">
                                        <!-- Header row -->
                                        <tr style="background:#f7f3ee;">
                                            <td style="padding:8px 12px;color:#9b9080;font-size:11px;letter-spacing:1px;text-transform:uppercase;border-radius:8px 0 0 0;">Product</td>
                                            <td style="padding:8px 12px;color:#9b9080;font-size:11px;letter-spacing:1px;text-transform:uppercase;text-align:center;">Qty</td>
                                            <td style="padding:8px 12px;color:#9b9080;font-size:11px;letter-spacing:1px;text-transform:uppercase;text-align:right;border-radius:0 8px 0 0;">Price</td>
                                        </tr>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td style="padding:12px 12px;color:#1e3a2f;font-size:13px;border-bottom:1px solid #f0ebe1;">
                                                <?= htmlspecialchars($item['product_name'] ?? $item['name'] ?? '') ?>
                                            </td>
                                            <td style="padding:12px 12px;color:#7a6e63;font-size:13px;text-align:center;border-bottom:1px solid #f0ebe1;">
                                                <?= (int)($item['quantity'] ?? 1) ?>
                                            </td>
                                            <td style="padding:12px 12px;color:#1e3a2f;font-size:13px;font-weight:600;text-align:right;border-bottom:1px solid #f0ebe1;">
                                                ₱<?= number_format(($item['price'] ?? $item['unit_price'] ?? 0) * ($item['quantity'] ?? 1), 2) ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>

                                    <!-- Totals -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                        <tr>
                                            <td style="padding:8px 12px;color:#7a6e63;font-size:13px;">Subtotal</td>
                                            <td style="padding:8px 12px;color:#1e3a2f;font-size:13px;text-align:right;">
                                                ₱<?= number_format((float)($order['subtotal'] ?? $order['total_amount']), 2) ?>
                                            </td>
                                        </tr>
                                        <?php if ((float)($order['delivery_fee'] ?? 0) > 0): ?>
                                        <tr>
                                            <td style="padding:4px 12px;color:#7a6e63;font-size:13px;">Delivery</td>
                                            <td style="padding:4px 12px;color:#1e3a2f;font-size:13px;text-align:right;">
                                                ₱<?= number_format((float)$order['delivery_fee'], 2) ?>
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <tr>
                                            <td style="padding:4px 12px;color:#7a6e63;font-size:13px;">Delivery</td>
                                            <td style="padding:4px 12px;color:#15803d;font-size:13px;text-align:right;">Free</td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ((float)($order['discount_amount'] ?? 0) > 0): ?>
                                        <tr>
                                            <td style="padding:4px 12px;color:#15803d;font-size:13px;">
                                                Discount<?= !empty($order['promo_code']) ? ' (' . htmlspecialchars($order['promo_code']) . ')' : '' ?>
                                            </td>
                                            <td style="padding:4px 12px;color:#15803d;font-size:13px;text-align:right;font-weight:600;">
                                                −₱<?= number_format((float)$order['discount_amount'], 2) ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <!-- Grand total -->
                                        <tr>
                                            <td colspan="2" style="padding-top:4px;">
                                                <table width="100%" cellpadding="0" cellspacing="0"
                                                       style="background:#f7f3ee;border-radius:10px;border:1px solid #e2d9cc;">
                                                    <tr>
                                                        <td style="padding:12px 16px;color:#1e3a2f;font-size:14px;font-family:'Georgia',serif;">Total</td>
                                                        <td style="padding:12px 16px;color:#1e3a2f;font-size:18px;font-weight:normal;text-align:right;font-family:'Georgia',serif;">
                                                            ₱<?= number_format((float)$order['total_amount'], 2) ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Divider -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                                        <tr>
                                            <td style="height:1px;background:linear-gradient(to right,transparent,#e2d9cc,transparent);"></td>
                                        </tr>
                                    </table>

                                    <!-- Delivery address -->
                                    <p style="margin:0 0 10px;color:#9b9080;font-size:11px;letter-spacing:2px;text-transform:uppercase;">Delivery Address</p>
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                        <tr>
                                            <td style="background:#f7f3ee;border:1px solid #e2d9cc;border-radius:12px;padding:14px 18px;">
                                                <p style="margin:0;color:#7a6e63;font-size:13px;line-height:1.7;">
                                                    <?= nl2br(htmlspecialchars($order['delivery_address'] ?? '')) ?>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- CTA Button -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                        <tr>
                                            <td align="center">
                                                <a href="<?= htmlspecialchars($appUrl) ?>/orders/<?= (int)$order['id'] ?>"
                                                   style="display:inline-block;background:#1e3a2f;color:#ffffff;text-decoration:none;font-family:'Georgia',serif;font-size:13px;letter-spacing:2px;text-transform:uppercase;padding:16px 44px;border-radius:50px;border:1px solid #2d5443;">
                                                    View My Order →
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Questions -->
                                    <p style="margin:0 0 16px;color:#9b9080;font-size:13px;text-align:center;">
                                        Questions? <a href="<?= htmlspecialchars($appUrl) ?>/contact" style="color:#1e3a2f;text-decoration:underline;">Contact us here</a>
                                    </p>

                                    <!-- Sign off -->
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
                                        <a href="<?= htmlspecialchars($appUrl) ?>" style="color:#c8bdb0;text-decoration:none;">petalsoul.com</a>
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