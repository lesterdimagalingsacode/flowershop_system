<?php
// app/views/payment/success.php
// Variables: $order (from PaymentController::success())
?>

<div class="bg-cream min-h-screen flex items-center justify-center py-16 px-6">
    <div class="max-w-md w-full text-center">

        <!-- Animated checkmark -->
        <div class="mb-8 flex justify-center">
            <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center animate-bounce-once">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>

        <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-2">Payment Confirmed</p>
        <h1 class="text-4xl text-text mb-3" style="font-family: var(--font-display);">
            Thank you, <?= e($order['first_name'] ?? 'there') ?>! 🌸
        </h1>
        <p class="text-muted text-sm mb-8">
            Your payment was received and your order is now confirmed.
            We'll start preparing it right away!
        </p>

        <!-- Order card -->
        <div class="bg-white border border-border rounded-2xl p-6 mb-6 text-left space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-muted">Order Number</span>
                <span class="font-semibold text-text"><?= e($order['order_number']) ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-muted">Total Paid</span>
                <span class="font-bold text-forest">₱<?= number_format($order['total_amount'], 2) ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-muted">Status</span>
                <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    Confirmed
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-muted">Payment</span>
                <span class="text-text">Online Payment</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col gap-3">
            <a href="<?= APP_URL ?>/orders/<?= (int)$order['id'] ?>"
               class="w-full bg-forest hover:bg-pine text-white font-medium text-sm px-6 py-3.5 rounded-full transition-all hover:-translate-y-px shadow-sm text-center">
                View My Order
            </a>
            <a href="<?= APP_URL ?>/shop"
               class="w-full bg-white border border-border hover:border-forest text-text font-medium text-sm px-6 py-3.5 rounded-full transition text-center">
                Continue Shopping
            </a>
        </div>

        <?php if (!empty($order['email'])): ?>
        <p class="text-xs text-muted mt-6">
            A confirmation email will be sent to <strong><?= e($order['email']) ?></strong>
        </p>
        <?php endif; ?>

    </div>
</div>

<style>
@keyframes bounce-once {
    0%, 100% { transform: translateY(0); }
    30%       { transform: translateY(-16px); }
    60%       { transform: translateY(-8px); }
}
.animate-bounce-once { animation: bounce-once 0.7s ease-out; }
</style>