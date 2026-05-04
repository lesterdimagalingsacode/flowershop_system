<?php $title = 'My Orders'; ?>

<div class="bg-cream min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-6">

        <!-- Header -->
        <div class="mb-8">
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-1">Account</p>
            <h1 class="text-4xl text-text" style="font-family: var(--font-display);">My Orders</h1>
        </div>

        <?php if (empty($orders)): ?>
        <div class="text-center py-24">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-2xl text-text mb-2" style="font-family: var(--font-display);">No orders yet</h3>
            <p class="text-muted text-sm mb-8">Start shopping and your orders will appear here.</p>
            <a href="<?= APP_URL ?>/shop"
               class="bg-forest hover:bg-pine text-white font-medium px-8 py-3 rounded-full text-sm transition-all hover:-translate-y-px shadow-sm">
                Browse the Shop
            </a>
        </div>

        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($orders as $order):
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
            <div class="bg-white border border-border rounded-2xl p-5 hover:shadow-md transition">
                <div class="flex items-start justify-between gap-4 flex-wrap">

                    <div>
                        <p class="text-xs text-muted mb-1"><?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                        <h3 class="font-semibold text-text" style="font-family: var(--font-display);">
                            <?= e($order['order_number']) ?>
                        </h3>
                        <p class="text-xs text-muted mt-1">
                            <?= e($order['delivery_address']) ?>
                        </p>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <span class="inline-block text-xs font-medium px-3 py-1 rounded-full border <?= $cls ?> capitalize mb-2">
                            <?= $order['status'] ?>
                        </span>
                        <p class="text-forest font-bold">₱<?= number_format($order['total_amount'], 2) ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-4 pt-4 border-t border-border">
                    <a href="<?= APP_URL ?>/orders/<?= $order['id'] ?>"
                       class="text-xs font-medium text-forest hover:text-pine transition">
                        View Details →
                    </a>
                    <?php if (in_array($order['status'], ['pending', 'confirmed'])): ?>
                    <form method="POST" action="<?= APP_URL ?>/orders/<?= $order['id'] ?>/cancel">
                        <?= csrf_field() ?>
                        <button type="submit"
                            class="text-xs text-muted hover:text-red-500 transition"
                            onclick="return confirm('Cancel this order?')">
                            Cancel Order
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-2 mt-8">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>"
               class="w-9 h-9 flex items-center justify-center rounded-full text-sm transition
                   <?= $i === $page
                       ? 'bg-forest text-white'
                       : 'border border-border text-muted hover:border-forest hover:text-forest' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>