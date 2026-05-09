<?php
// app/views/emails/order-status.php
// Variables available: $order, $customer, $newStatus, $note
// Called from OrderController::updateStatus()
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Update — Petal &amp; Soul</title>
    <style>
        body        { margin:0; padding:0; background:#f9fafb; font-family:'Segoe UI',Arial,sans-serif; color:#374151; }
        .wrapper    { max-width:580px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; border:1px solid #e5e7eb; }
        .header     { background:#ec4899; padding:28px 32px; text-align:center; }
        .header h1  { margin:0; color:#fff; font-size:22px; font-weight:700; }
        .header p   { margin:6px 0 0; color:#fce7f3; font-size:13px; }
        .body       { padding:32px; }
        .body p     { font-size:14px; line-height:1.6; margin:0 0 12px; color:#4b5563; }
        .status-box { border-radius:12px; padding:20px 24px; margin:20px 0; text-align:center; }
        .status-box .label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; margin:0 0 6px; }
        .status-box .value { font-size:24px; font-weight:800; margin:0; }
        .note-box   { background:#f9fafb; border-left:3px solid #e5e7eb; border-radius:0 8px 8px 0; padding:12px 16px; margin:16px 0; font-size:13px; color:#6b7280; font-style:italic; }
        .timeline   { margin:24px 0; }
        .step       { display:flex; align-items:center; gap:12px; margin-bottom:12px; font-size:13px; }
        .dot        { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
        .dot.done   { background:#ec4899; }
        .dot.active { background:#f97316; box-shadow:0 0 0 3px #fed7aa; }
        .dot.pending{ background:#e5e7eb; }
        .btn        { display:inline-block; background:#ec4899; color:#fff; text-decoration:none; padding:12px 28px; border-radius:10px; font-weight:700; font-size:14px; margin:16px 0; }
        .footer     { padding:20px 32px; border-top:1px solid #f3f4f6; text-align:center; }
        .footer p   { font-size:12px; color:#9ca3af; margin:0; }
        .footer a   { color:#ec4899; text-decoration:none; }
    </style>
</head>
<body>
<?php
// ── Status config ──────────────────────────────────────────────────────────
$statusConfig = [
    'confirmed'   => ['bg' => '#fdf2f8', 'color' => '#db2777', 'emoji' => '✅', 'label' => 'Order Confirmed',       'msg' => "We've confirmed your order and it's now being prepared."],
    'processing'  => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'emoji' => '🌸', 'label' => 'Being Prepared',        'msg' => 'Our team is carefully arranging your flowers.'],
    'ready'       => ['bg' => '#f0fdf4', 'color' => '#15803d', 'emoji' => '🎁', 'label' => 'Ready for Pickup',      'msg' => 'Your order is packed and ready to be picked up by the rider.'],
    'out_delivery'=> ['bg' => '#fff7ed', 'color' => '#c2410c', 'emoji' => '🚴', 'label' => 'Out for Delivery',      'msg' => "Your order is on its way! The rider is heading to your address."],
    'delivered'   => ['bg' => '#f0fdf4', 'color' => '#15803d', 'emoji' => '🎉', 'label' => 'Delivered!',            'msg' => 'Your order has been delivered. We hope you love it!'],
    'cancelled'   => ['bg' => '#fef2f2', 'color' => '#dc2626', 'emoji' => '❌', 'label' => 'Order Cancelled',       'msg' => 'Your order has been cancelled. Contact us if you have questions.'],
];

$allSteps = ['confirmed', 'processing', 'ready', 'out_delivery', 'delivered'];
$cfg      = $statusConfig[$newStatus] ?? ['bg' => '#f9fafb', 'color' => '#374151', 'emoji' => '📦', 'label' => ucfirst($newStatus), 'msg' => 'Your order status has been updated.'];
?>

<div class="wrapper">

    <div class="header">
        <h1>🌸 Petal &amp; Soul</h1>
        <p>Your order status has been updated</p>
    </div>

    <div class="body">
        <p>Hi <strong><?= htmlspecialchars($customer['name']) ?></strong>,</p>
        <p>Here's an update on your order <strong>#<?= $order['id'] ?></strong>:</p>

        <!-- Status badge -->
        <div class="status-box" style="background:<?= $cfg['bg'] ?>;">
            <p class="label" style="color:<?= $cfg['color'] ?>;">Current Status</p>
            <p class="value" style="color:<?= $cfg['color'] ?>;">
                <?= $cfg['emoji'] ?> <?= $cfg['label'] ?>
            </p>
        </div>

        <p><?= $cfg['msg'] ?></p>

        <!-- Admin note (optional) -->
        <?php if (! empty($note)): ?>
            <p style="font-weight:600;font-size:13px;color:#6b7280;">Note from our team:</p>
            <div class="note-box"><?= nl2br(htmlspecialchars($note)) ?></div>
        <?php endif; ?>

        <!-- Progress timeline (skip for cancelled) -->
        <?php if ($newStatus !== 'cancelled'): ?>
            <p style="font-weight:600;font-size:13px;color:#6b7280;margin-top:24px;text-transform:uppercase;letter-spacing:.05em;">Order Progress</p>
            <div class="timeline">
                <?php
                $currentIndex = array_search($newStatus, $allSteps);
                $stepLabels   = [
                    'confirmed'    => 'Confirmed',
                    'processing'   => 'Being Prepared',
                    'ready'        => 'Ready for Pickup',
                    'out_delivery' => 'Out for Delivery',
                    'delivered'    => 'Delivered',
                ];
                foreach ($allSteps as $i => $step):
                    if ($i < $currentIndex)  $dotClass = 'done';
                    elseif ($i === $currentIndex) $dotClass = 'active';
                    else                      $dotClass = 'pending';
                    $textColor = $dotClass === 'pending' ? '#9ca3af' : '#374151';
                    $weight    = $dotClass === 'active'  ? '700' : '400';
                ?>
                    <div class="step">
                        <span class="dot <?= $dotClass ?>"></span>
                        <span style="color:<?= $textColor ?>;font-weight:<?= $weight ?>;">
                            <?= $stepLabels[$step] ?>
                            <?= $dotClass === 'active' ? ' ← You are here' : '' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="text-align:center;margin-top:24px;">
            <a href="<?= APP_URL ?>/orders/<?= $order['id'] ?>" class="btn">View Order Details →</a>
        </div>

        <p style="margin-top:24px;font-size:13px;color:#9ca3af;">
            Questions? <a href="<?= APP_URL ?>/contact" style="color:#ec4899;">Contact us here</a>.
        </p>
        <p>With love,<br/><strong>The Petal &amp; Soul Team 🌸</strong></p>
    </div>

    <div class="footer">
        <p>Baler, Aurora, Philippines &nbsp;·&nbsp; <a href="<?= APP_URL ?>">petalsoul.com</a></p>
    </div>

</div>
</body>
</html>