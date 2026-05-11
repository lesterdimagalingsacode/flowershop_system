<?php
// app/views/payment/failed.php
// Variables: $order (may be null), $cancelled (bool)
$isCancelled = $cancelled ?? false;
?>

<div class="bg-cream min-h-screen flex items-center justify-center py-16 px-6">
    <div class="max-w-md w-full text-center">

        <!-- Icon -->
        <div class="mb-8 flex justify-center">
            <div class="w-24 h-24 rounded-full <?= $isCancelled ? 'bg-amber-100' : 'bg-red-100' ?> flex items-center justify-center">
                <?php if ($isCancelled): ?>
                    <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                <?php else: ?>
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                    </svg>
                <?php endif; ?>
            </div>
        </div>

        <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-2">
            <?= $isCancelled ? 'Payment Cancelled' : 'Payment Failed' ?>
        </p>
        <h1 class="text-4xl text-text mb-3" style="font-family: var(--font-display);">
            <?= $isCancelled ? 'No worries!' : 'Something went wrong' ?>
        </h1>
        <p class="text-muted text-sm mb-8">
            <?php if ($isCancelled): ?>
                You cancelled the payment. Your order is still saved — you can retry anytime.
            <?php else: ?>
                Your payment could not be processed. No charges were made.
                Please try again or use a different payment method.
            <?php endif; ?>
        </p>

        <?php if ($order): ?>
        <div class="bg-white border border-border rounded-2xl p-6 mb-6 text-left space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-muted">Order Number</span>
                <span class="font-semibold text-text"><?= e($order['order_number']) ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-muted">Total</span>
                <span class="font-bold text-text">₱<?= number_format($order['total_amount'], 2) ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-muted">Status</span>
                <span class="inline-flex items-center gap-1 text-amber-600 font-medium">
                    <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                    Payment Pending
                </span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="flex flex-col gap-3">
            <?php if ($order): ?>
            <button onclick="RetryModal.open()"
                class="w-full bg-forest hover:bg-pine text-white font-medium text-sm px-6 py-3.5 rounded-full transition-all hover:-translate-y-px shadow-sm text-center">
                Retry Payment
            </button>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/shop"
               class="w-full bg-white border border-border hover:border-forest text-text font-medium text-sm px-6 py-3.5 rounded-full transition text-center">
                Back to Shop
            </a>
        </div>

        <p class="text-xs text-muted mt-6">
            Need help? <a href="<?= APP_URL ?>/contact" class="text-forest hover:underline">Contact us</a>
        </p>

        <!-- ADD THIS -->
        <?php if ($order): ?>
        <?php
          $retryOrderId     = $order['id'];
          $retryOrderNumber = $order['order_number'];
          $retryTotal       = $order['total_amount'];
          include __DIR__ . '/../partials/retry_payment_modal.php';
        ?>
        <?php endif; ?>

    </div>
</div>