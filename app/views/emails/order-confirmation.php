<?php
// app/views/emails/order-confirmation.php
// Variables available: $order, $items, $customer
// Called from OrderController::checkout() after order is placed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmed — Petal &amp; Soul</title>
    <style>
        body        { margin:0; padding:0; background:#f9fafb; font-family:'Segoe UI',Arial,sans-serif; color:#374151; }
        .wrapper    { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; border:1px solid #e5e7eb; }
        .header     { background:#ec4899; padding:28px 32px; text-align:center; }
        .header h1  { margin:0; color:#fff; font-size:22px; font-weight:700; }
        .header p   { margin:6px 0 0; color:#fce7f3; font-size:13px; }
        .body       { padding:32px; }
        .body p     { font-size:14px; line-height:1.6; margin:0 0 12px; color:#4b5563; }
        .order-box  { background:#fdf2f8; border:1px solid #fbcfe8; border-radius:12px; padding:16px 20px; margin:20px 0; }
        .order-box p{ margin:0; font-size:13px; color:#9d174d; }
        .order-box .order-num { font-size:22px; font-weight:800; color:#db2777; margin:4px 0 0; }
        table.items { width:100%; border-collapse:collapse; margin:20px 0; font-size:13px; }
        table.items th { text-align:left; padding:8px 10px; background:#f3f4f6; color:#6b7280; font-weight:600; border-radius:6px; }
        table.items td { padding:10px 10px; border-bottom:1px solid #f3f4f6; color:#374151; vertical-align:top; }
        table.items td.qty  { text-align:center; color:#6b7280; }
        table.items td.price{ text-align:right; font-weight:600; }
        .totals     { margin-top:8px; }
        .totals tr td { padding:6px 10px; font-size:13px; }
        .totals tr td:last-child { text-align:right; font-weight:600; }
        .totals tr.grand td { font-size:15px; color:#db2777; border-top:2px solid #fbcfe8; padding-top:12px; }
        .address-box{ background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:14px 18px; margin:20px 0; font-size:13px; color:#4b5563; line-height:1.7; }
        .badge      { display:inline-block; background:#fef3c7; color:#92400e; font-size:12px; font-weight:600; padding:3px 10px; border-radius:99px; }
        .footer     { padding:20px 32px; border-top:1px solid #f3f4f6; text-align:center; }
        .footer p   { font-size:12px; color:#9ca3af; margin:0; }
        .footer a   { color:#ec4899; text-decoration:none; }
        .btn        { display:inline-block; background:#ec4899; color:#fff; text-decoration:none; padding:12px 28px; border-radius:10px; font-weight:700; font-size:14px; margin:16px 0; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>🌸 Petal &amp; Soul</h1>
        <p>Your order has been placed!</p>
    </div>

    <div class="body">
        <p>Hi <strong><?= htmlspecialchars($customer['name']) ?></strong>,</p>
        <p>Thank you for your order! We've received it and will start preparing it shortly. Here's your order summary:</p>

        <!-- Order number + status -->
        <div class="order-box">
            <p>Order Number</p>
            <p class="order-num">#<?= $order['id'] ?></p>
            <p style="margin-top:8px;">
                Payment: <span class="badge"><?= ucfirst(htmlspecialchars($order['payment_method'] ?? 'COD')) ?></span>
            </p>
        </div>

        <!-- Items table -->
        <table class="items">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="qty"><?= (int) $item['quantity'] ?></td>
                        <td class="price">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <table class="totals" style="width:100%;border-collapse:collapse;">
            <tr>
                <td style="color:#6b7280;">Subtotal</td>
                <td style="text-align:right;font-weight:600;">₱<?= number_format($order['subtotal'] ?? $order['total_amount'], 2) ?></td>
            </tr>
            <?php if (! empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                <tr>
                    <td style="color:#16a34a;">Discount</td>
                    <td style="text-align:right;font-weight:600;color:#16a34a;">−₱<?= number_format($order['discount_amount'], 2) ?></td>
                </tr>
            <?php endif; ?>
            <tr class="grand">
                <td><strong>Total</strong></td>
                <td style="text-align:right;font-weight:800;color:#db2777;font-size:15px;">₱<?= number_format($order['total_amount'], 2) ?></td>
            </tr>
        </table>

        <!-- Delivery address -->
        <p style="margin-top:20px;font-weight:600;font-size:13px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Delivery Address</p>
        <div class="address-box">
            <?= nl2br(htmlspecialchars($order['delivery_address'])) ?>
        </div>

        <div style="text-align:center;margin-top:24px;">
            <a href="<?= APP_URL ?>/orders/<?= $order['id'] ?>" class="btn">View My Order →</a>
        </div>

        <p style="margin-top:24px;font-size:13px;color:#9ca3af;">
            If you have any questions, <a href="<?= APP_URL ?>/contact" style="color:#ec4899;">contact us here</a>
            or reply to this email.
        </p>
        <p>With love,<br/><strong>The Petal &amp; Soul Team 🌸</strong></p>
    </div>

    <div class="footer">
        <p>Baler, Aurora, Philippines &nbsp;·&nbsp; <a href="<?= APP_URL ?>">petalsoul.com</a></p>
    </div>

</div>
</body>
</html>