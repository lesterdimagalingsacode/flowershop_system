<?php
// app/views/emails/order-status.php
// Variables available: $order, $customer, $newStatus, $note
// Called from OrderController::updateStatus()

$statusConfig = [
    'confirmed'  => [
        'bg'    => '#f0fdf4', 'color' => '#15803d', 'border' => '#bbf7d0',
        'emoji' => '✅', 'label' => 'Order Confirmed',
        'msg'   => "Great news! We've confirmed your order and our team is getting ready to prepare your blooms.",
    ],
    'processing' => [
        'bg'    => '#fdf2f8', 'color' => '#9d174d', 'border' => '#fbcfe8',
        'emoji' => '🌸', 'label' => 'Being Prepared',
        'msg'   => 'Our florists are carefully arranging your flowers with love and attention to detail.',
    ],
    'ready'      => [
        'bg'    => '#fffbeb', 'color' => '#92400e', 'border' => '#fde68a',
        'emoji' => '🎁', 'label' => 'Ready for Delivery',
        'msg'   => 'Your order is beautifully packed and ready to be picked up by our delivery partner.',
    ],
    'delivered'  => [
        'bg'    => '#f0fdf4', 'color' => '#15803d', 'border' => '#bbf7d0',
        'emoji' => '🎉', 'label' => 'Delivered!',
        'msg'   => 'Your order has been delivered. We hope it brings as much joy as we put into preparing it!',
    ],
    'cancelled'  => [
        'bg'    => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca',
        'emoji' => '❌', 'label' => 'Order Cancelled',
        'msg'   => 'Your order has been cancelled. If you have any questions, please don\'t hesitate to reach out.',
    ],
];

$allSteps = ['confirmed', 'processing', 'ready', 'delivered'];
$stepLabels = [
    'confirmed'  => 'Confirmed',
    'processing' => 'Being Prepared',
    'ready'      => 'Ready for Delivery',
    'delivered'  => 'Delivered',
];

$cfg = $statusConfig[$newStatus] ?? [
    'bg' => '#f7f3ee', 'color' => '#1e3a2f', 'border' => '#e2d9cc',
    'emoji' => '📦', 'label' => ucfirst($newStatus),
    'msg' => 'Your order status has been updated.',
];

$appName = APP_NAME ?? 'Petal & Soul';
$appUrl  = APP_URL  ?? '#';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Order Update — <?= htmlspecialchars($appName) ?></title>
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
                                        Order Status Update
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Status badge -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:32px 48px 0;">
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="background:<?= $cfg['bg'] ?>;border:1px solid <?= $cfg['border'] ?>;border-radius:16px;">
                                        <tr>
                                            <td style="padding:24px;text-align:center;">
                                                <p style="margin:0 0 6px;font-size:28px;"><?= $cfg['emoji'] ?></p>
                                                <p style="margin:0 0 4px;color:<?= $cfg['color'] ?>;font-size:11px;letter-spacing:3px;text-transform:uppercase;font-family:'Georgia',serif;">
                                                    Current Status
                                                </p>
                                                <p style="margin:0;color:<?= $cfg['color'] ?>;font-size:22px;font-weight:normal;font-family:'Georgia',serif;">
                                                    <?= $cfg['label'] ?>
                                                </p>
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
                                    <p style="margin:0 0 6px;color:#7a6e63;font-size:14px;line-height:1.7;">
                                        Here's an update on your order
                                        <strong style="color:#1e3a2f;"><?= htmlspecialchars($order['order_number'] ?? '#' . $order['id']) ?></strong>:
                                    </p>
                                    <p style="margin:0 0 24px;color:#7a6e63;font-size:14px;line-height:1.7;">
                                        <?= $cfg['msg'] ?>
                                    </p>

                                    <!-- Admin note -->
                                    <?php if (!empty($note)): ?>
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                        <tr>
                                            <td style="background:#fdf8f0;border-left:3px solid #c4973a;border-radius:0 10px 10px 0;padding:14px 18px;">
                                                <p style="margin:0 0 4px;color:#92400e;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;">Note from our team</p>
                                                <p style="margin:0;color:#7a6e63;font-size:13px;line-height:1.6;font-style:italic;">
                                                    <?= nl2br(htmlspecialchars($note)) ?>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php endif; ?>

                                    <!-- Divider -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                        <tr>
                                            <td style="height:1px;background:linear-gradient(to right,transparent,#e2d9cc,transparent);"></td>
                                        </tr>
                                    </table>

                                    <!-- Progress timeline (skip for cancelled) -->
                                    <?php if ($newStatus !== 'cancelled'): ?>
                                    <p style="margin:0 0 16px;color:#9b9080;font-size:11px;letter-spacing:2px;text-transform:uppercase;">
                                        Order Progress
                                    </p>
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                        <?php
                                        $currentIndex = array_search($newStatus, $allSteps);
                                        foreach ($allSteps as $i => $step):
                                            if ($i < $currentIndex)       $state = 'done';
                                            elseif ($i === $currentIndex) $state = 'active';
                                            else                          $state = 'pending';

                                            $dotBg    = $state === 'done'   ? '#1e3a2f'
                                                      : ($state === 'active' ? '#c4973a' : '#e2d9cc');
                                            $textColor = $state === 'pending' ? '#c8bdb0' : '#1e3a2f';
                                            $weight    = $state === 'active'  ? '600' : '400';
                                            $isLast    = ($i === count($allSteps) - 1);
                                        ?>
                                        <tr>
                                            <td style="width:24px;padding-bottom:<?= $isLast ? '0' : '4px' ?>;">
                                                <table cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="center" style="width:24px;">
                                                            <!-- Dot -->
                                                            <div style="width:10px;height:10px;border-radius:50%;background:<?= $dotBg ?>;margin:0 auto;<?= $state === 'active' ? 'box-shadow:0 0 0 3px rgba(196,151,58,0.25);' : '' ?>"></div>
                                                            <?php if (!$isLast): ?>
                                                            <!-- Connector line -->
                                                            <div style="width:1px;height:20px;background:<?= $i < $currentIndex ? '#1e3a2f' : '#e2d9cc' ?>;margin:2px auto 2px;"></div>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="padding:0 0 <?= $isLast ? '0' : '4px' ?> 12px;vertical-align:top;">
                                                <p style="margin:0;color:<?= $textColor ?>;font-size:13px;font-weight:<?= $weight ?>;line-height:1;padding-top:0px;">
                                                    <?= $stepLabels[$step] ?>
                                                    <?php if ($state === 'active'): ?>
                                                        <span style="color:#c4973a;font-size:11px;font-weight:400;"> ← You are here</span>
                                                    <?php endif; ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>

                                    <!-- Divider -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                        <tr>
                                            <td style="height:1px;background:linear-gradient(to right,transparent,#e2d9cc,transparent);"></td>
                                        </tr>
                                    </table>
                                    <?php endif; ?>

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