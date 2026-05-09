<?php $title = 'Order ' . $order['order_number']; ?>

<div class="bg-cream min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-6">

        <!-- Back -->
        <a href="<?= APP_URL ?>/orders"
           class="inline-flex items-center gap-2 text-xs text-muted hover:text-forest transition mb-6">
            ← Back to Orders
        </a>

        <!-- Header -->
        <div class="flex items-start justify-between gap-4 flex-wrap mb-8">
            <div>
                <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-1">Order Details</p>
                <h1 class="text-4xl text-text" style="font-family: var(--font-display);">
                    <?= e($order['order_number']) ?>
                </h1>
                <p class="text-muted text-sm mt-1">
                    Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                </p>
            </div>

            <?php
            $statusColors = [
                'pending'    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'confirmed'  => 'bg-blue-50 text-blue-700 border-blue-200',
                'processing' => 'bg-purple-50 text-purple-700 border-purple-200',
                'ready'      => 'bg-teal-50 text-teal-700 border-teal-200',
                'delivered'  => 'bg-green-50 text-green-700 border-green-200',
                'cancelled'  => 'bg-red-50 text-red-700 border-red-200',
            ];
            $cls = $statusColors[$order['status']] ?? 'bg-gray-50 text-gray-700 border-gray-200';
            ?>
            <span class="text-sm font-medium px-4 py-1.5 rounded-full border <?= $cls ?> capitalize">
                <?= $order['status'] ?>
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left — Items + Status history -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Order items -->
                <div class="bg-white border border-border rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-border">
                        <h2 class="font-semibold text-text" style="font-family: var(--font-display);">Items Ordered</h2>
                    </div>
                    <?php foreach ($items as $item): ?>
                    <div class="flex items-center gap-4 p-5 border-b border-border last:border-b-0">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-ivory flex-shrink-0">
                            <?php
                            $imageUrl = $item['image']
                                ? APP_URL . '/images/products/' . e($item['image'])
                                : 'https://picsum.photos/seed/' . $item['product_id'] . '/200/200';
                            ?>
                            <img src="<?= $imageUrl ?>" alt="<?= e($item['name']) ?>"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-text line-clamp-1"><?= e($item['name']) ?></h3>
                            <p class="text-xs text-muted mt-0.5">
                                ₱<?= number_format($item['unit_price'], 2) ?> × <?= $item['quantity'] ?>
                            </p>
                        </div>
                        <p class="font-bold text-text flex-shrink-0">
                            ₱<?= number_format($item['subtotal'], 2) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Status history -->
                <?php if (!empty($history)): ?>
                <div class="bg-white border border-border rounded-2xl p-5">
                    <h2 class="font-semibold text-text mb-5" style="font-family: var(--font-display);">Order Timeline</h2>
                    <div class="relative">
                        <!-- Vertical line -->
                        <div class="absolute left-3 top-0 bottom-0 w-px bg-border"></div>
                        <div class="space-y-5">
                            <?php foreach ($history as $h): ?>
                            <div class="flex items-start gap-4 pl-8 relative">
                                <!-- Dot -->
                                <div class="absolute left-0 w-6 h-6 rounded-full bg-forest/10 border-2 border-forest flex items-center justify-center flex-shrink-0">
                                    <div class="w-2 h-2 rounded-full bg-forest"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-text capitalize">
                                        <?= e($h['to_status']) ?>
                                    </p>
                                    <?php if ($h['notes']): ?>
                                    <p class="text-xs text-muted mt-0.5"><?= e($h['notes']) ?></p>
                                    <?php endif; ?>
                                    <p class="text-[0.65rem] text-subtle mt-1">
                                        <?= date('M j, Y g:i A', strtotime($h['created_at'])) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Right — Summary + Actions -->
            <div class="space-y-5">

                <!-- Order summary -->
                <div class="bg-white border border-border rounded-2xl p-5">
                    <h2 class="font-semibold text-text mb-4" style="font-family: var(--font-display);">Summary</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted">Subtotal</span>
                            <span class="text-text">₱<?= number_format($order['subtotal'], 2) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Delivery</span>
                            <span class="text-text">
                                <?= (float)$order['delivery_fee'] > 0
                                    ? '₱' . number_format($order['delivery_fee'], 2)
                                    : 'Free' ?>
                            </span>
                        </div>
                        <?php if ((float)($order['discount_amount'] ?? 0) > 0): ?>
                        <div class="flex justify-between">
                            <span class="text-muted">Discount</span>
                            <span class="text-green-600">−₱<?= number_format($order['discount_amount'], 2) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between pt-2 border-t border-border">
                            <span class="font-semibold text-text">Total</span>
                            <span class="font-bold text-forest">₱<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>

                    <!-- Payment method badge -->
                    <div class="mt-4 pt-4 border-t border-border flex items-center justify-between text-xs text-muted">
                        <span>Payment</span>
                        <span class="font-medium text-text capitalize">
                            <?= $order['payment_method'] === 'online' ? '💳 Online Payment' : '💵 Cash on Delivery' ?>
                        </span>
                    </div>
                </div>

                <!-- Delivery info -->
                <div class="bg-white border border-border rounded-2xl p-5">
                    <h2 class="font-semibold text-text mb-3" style="font-family: var(--font-display);">Delivery</h2>
                    <p class="text-sm text-muted leading-relaxed"><?= e($order['delivery_address']) ?></p>
                    <?php if ($order['notes']): ?>
                    <div class="mt-3 pt-3 border-t border-border">
                        <p class="text-xs text-muted uppercase tracking-wider mb-1">Notes</p>
                        <p class="text-sm text-text"><?= e($order['notes']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ── Pay Now (pending online payment only) ── -->
                <?php if ($order['status'] === 'pending' && $order['payment_method'] === 'online'): ?>
                <form method="POST" action="<?= APP_URL ?>/payment/retry/<?= (int)$order['id'] ?>">
                    <?= csrf_field() ?>
                    <button type="submit"
                        class="w-full bg-forest hover:bg-pine text-white text-sm font-semibold py-3 rounded-full transition-all hover:-translate-y-px shadow-sm">
                        💳 Complete Payment
                    </button>
                </form>
                <p class="text-xs text-muted text-center -mt-2">
                    Your order is saved. Complete payment to confirm it.
                </p>
                <?php endif; ?>

                <!-- Cancel button -->
                <?php if (in_array($order['status'], ['pending', 'confirmed'])): ?>
                <form method="POST" action="<?= APP_URL ?>/orders/<?= $order['id'] ?>/cancel">
                    <?= csrf_field() ?>
                    <button type="submit"
                        class="w-full border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium py-2.5 rounded-full transition"
                        onclick="return confirm('Are you sure you want to cancel this order?')">
                        Cancel Order
                    </button>
                </form>
                <?php endif; ?>

                <a href="<?= APP_URL ?>/shop"
                   class="block w-full bg-white border border-border hover:border-forest text-text text-sm font-medium text-center py-2.5 rounded-full transition">
                    Continue Shopping
                </a>

            </div>
        </div>
    </div>
</div>