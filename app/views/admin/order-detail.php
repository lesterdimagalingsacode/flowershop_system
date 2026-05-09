<?php
// app/views/admin/order-detail.php
// Layout: admin

$statusMap = [
    'pending'    => ['label' => 'Pending',    'class' => 'bg-yellow-100 text-yellow-700 border-yellow-200'],
    'confirmed'  => ['label' => 'Confirmed',  'class' => 'bg-blue-100 text-blue-700 border-blue-200'],
    'processing' => ['label' => 'Processing', 'class' => 'bg-indigo-100 text-indigo-700 border-indigo-200'],
    'ready'      => ['label' => 'Ready',      'class' => 'bg-teal-100 text-teal-700 border-teal-200'],
    'delivered'  => ['label' => 'Delivered',  'class' => 'bg-green-100 text-green-700 border-green-200'],
    'cancelled'  => ['label' => 'Cancelled',  'class' => 'bg-red-100 text-red-700 border-red-200'],
];

$currentStatus = $order['status'] ?? 'pending';
$badge         = $statusMap[$currentStatus] ?? ['label' => ucfirst($currentStatus), 'class' => 'bg-gray-100 text-gray-600 border-gray-200'];
$isFinal       = in_array($currentStatus, ['delivered', 'cancelled']);

// ── Check if online payment is still unpaid ──
// Blocks admin from confirming before customer pays
$isOnline        = ($order['payment_method'] ?? '') === 'online';
$paymentUnpaid   = false;
$paymentStatus   = null;

if ($isOnline) {
    $db            = Database::getInstance();
    $paymentRecord = $db->queryOne(
        "SELECT status, payment_method as paid_via, paid_at FROM payments WHERE order_id = ? LIMIT 1",
        [(int)$order['id']]
    );
    $paymentStatus = $paymentRecord['status'] ?? null;
    $paymentUnpaid = ($paymentStatus !== 'paid');
}
?>

<div class="max-w-5xl mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <a href="<?= APP_URL ?>/admin/orders"
               class="inline-flex items-center gap-1 text-xs tracking-widest uppercase text-gray-400 hover:text-gray-600 transition mb-2">
                ← Back to Orders
            </a>
            <h1 class="text-2xl font-bold text-gray-900">
                <?= htmlspecialchars($order['order_number'] ?? '') ?>
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">
                <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
            </p>
        </div>
        <span class="self-start sm:self-center inline-block px-4 py-1.5 rounded-full text-sm font-semibold border <?= $badge['class'] ?>">
            <?= $badge['label'] ?>
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT: Items + Summary -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Order Items -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Order Items</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php foreach ($items as $item): ?>
                        <div class="flex items-center gap-4 px-6 py-4">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= APP_URL ?>/<?= htmlspecialchars($item['image']) ?>"
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     class="w-14 h-14 rounded-xl object-cover flex-shrink-0 border border-gray-100">
                            <?php else: ?>
                                <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-gray-300 text-xl">🌸</span>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-800 truncate">
                                    <?= htmlspecialchars($item['name']) ?>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    ₱<?= number_format((float)$item['unit_price'], 2) ?> × <?= (int)$item['quantity'] ?>
                                </div>
                            </div>
                            <div class="font-semibold text-gray-800 flex-shrink-0">
                                ₱<?= number_format((float)$item['subtotal'], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Price Breakdown -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal</span>
                        <span>₱<?= number_format((float)$order['subtotal'], 2) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Delivery fee</span>
                        <span>₱<?= number_format((float)$order['delivery_fee'], 2) ?></span>
                    </div>
                    <?php if ((float)($order['discount_amount'] ?? 0) > 0): ?>
                        <div class="flex justify-between text-green-600">
                            <span>
                                Discount
                                <?php if (!empty($order['promo_code'])): ?>
                                    <span class="ml-1 px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs font-mono">
                                        <?= htmlspecialchars($order['promo_code']) ?>
                                    </span>
                                <?php endif; ?>
                            </span>
                            <span>−₱<?= number_format((float)$order['discount_amount'], 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between font-bold text-gray-900 text-base pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span>₱<?= number_format((float)$order['total_amount'], 2) ?></span>
                    </div>

                    <!-- Payment status row -->
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200 text-xs">
                        <span class="text-gray-400">Payment</span>
                        <span class="flex items-center gap-1.5 font-medium">
                            <?php if ($isOnline): ?>
                                <?php if ($paymentStatus === 'paid'): ?>
                                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                    <span class="text-green-600">Paid via Online</span>
                                <?php else: ?>
                                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                                    <span class="text-amber-600">Awaiting Payment</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                                <span class="text-gray-600">Cash on Delivery</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Status History -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Status History</h2>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <?php if (empty($history)): ?>
                        <p class="text-sm text-gray-400">No history yet.</p>
                    <?php else: ?>
                        <?php foreach (array_reverse($history) as $h): ?>
                            <div class="flex gap-3 text-sm">
                                <div class="flex-shrink-0 w-2 h-2 rounded-full bg-pink-400 mt-1.5"></div>
                                <div>
                                    <div class="font-medium text-gray-800">
                                        <?php if (!empty($h['from_status'])): ?>
                                            <?= ucfirst(htmlspecialchars($h['from_status'])) ?> →
                                        <?php endif; ?>
                                        <?= ucfirst(htmlspecialchars($h['to_status'] ?? '')) ?>
                                    </div>
                                    <?php if (!empty($h['notes'])): ?>
                                        <div class="text-gray-500 text-xs mt-0.5">
                                            <?= htmlspecialchars($h['notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-gray-400 text-xs mt-0.5">
                                        <?= date('M j, Y g:i A', strtotime($h['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- RIGHT: Customer + Delivery + Update Status -->
        <div class="space-y-6">

            <!-- Customer Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Customer</h2>
                <div class="space-y-1 text-sm">
                    <div class="font-medium text-gray-900">
                        <?= htmlspecialchars(
                            $order['customer_name']
                            ?? trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''))
                            ?: 'Unknown'
                        ) ?>
                    </div>
                    <?php if (!empty($order['customer_email'] ?? $order['email'] ?? '')): ?>
                        <div class="text-gray-400">
                            <?= htmlspecialchars($order['customer_email'] ?? $order['email']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold text-gray-800 mb-3">Delivery Address</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($order['delivery_address'] ?? '—')) ?>
                </p>
                <?php if (!empty($order['notes'])): ?>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Notes</div>
                        <p class="text-sm text-gray-600"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Update Status -->
            <?php if (!$isFinal): ?>
                <?php
                    $transitions = [
                        'pending'    => ['confirmed' => '✓ Confirm Order',      'cancelled' => '✗ Cancel'],
                        'confirmed'  => ['processing' => '⚙ Start Processing',  'cancelled' => '✗ Cancel'],
                        'processing' => ['ready'      => '📦 Mark Ready',       'cancelled' => '✗ Cancel'],
                        'ready'      => ['delivered'  => '✅ Mark Delivered'],
                    ];
                    $nextSteps = $transitions[$currentStatus] ?? [];
                ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Update Status</h2>

                    <!-- ── Unpaid online order warning ── -->
                    <?php if ($isOnline && $paymentUnpaid && $currentStatus === 'pending'): ?>
                    <div class="mb-4 flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-700">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span>Payment not yet received. You can only cancel this order until the customer completes payment.</span>
                    </div>
                    <?php endif; ?>

                    <div id="statusMsg" class="hidden mb-3 text-sm px-3 py-2 rounded-lg"></div>

                    <!-- Optional note -->
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Note (optional)</label>
                        <textarea id="statusNotes" rows="2" placeholder="Add a note…"
                            class="w-full text-sm border border-border rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-forest/20 resize-none"></textarea>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex flex-col gap-2">
                        <?php foreach ($nextSteps as $status => $label): ?>
                            <?php
                                $isDanger  = $status === 'cancelled';
                                // Block confirm for unpaid online orders
                                $isBlocked = ($isOnline && $paymentUnpaid && $status === 'confirmed');
                            ?>
                            <?php if ($isBlocked): ?>
                                <!-- Disabled confirm button with tooltip -->
                                <button disabled
                                    title="Cannot confirm — payment not yet received"
                                    class="w-full px-4 py-2.5 text-sm font-semibold rounded-xl
                                           bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed">
                                    <?= $label ?> <span class="text-xs font-normal">(awaiting payment)</span>
                                </button>
                            <?php else: ?>
                                <button
                                    data-status="<?= $status ?>"
                                    class="status-btn w-full px-4 py-2.5 text-sm font-semibold rounded-xl transition
                                        <?= $isDanger
                                            ? 'bg-white border border-red-200 text-red-500 hover:bg-red-50'
                                            : 'bg-forest hover:bg-pine text-white' ?>">
                                    <?= $label ?>
                                </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
(function () {
    const buttons = document.querySelectorAll('.status-btn');
    const notes   = document.getElementById('statusNotes');
    const msg     = document.getElementById('statusMsg');
    if (!buttons.length) return;

    buttons.forEach(btn => {
        btn.addEventListener('click', async function () {
            const status = this.dataset.status;

            if (status === 'cancelled' && !confirm('Cancel this order?')) return;

            buttons.forEach(b => b.disabled = true);
            this.textContent = 'Updating…';

            try {
                const res = await fetch('<?= APP_URL ?>/admin/orders/<?= (int)$order['id'] ?>/status', {
                    method : 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body   : new URLSearchParams({
                        '<?= CSRF_TOKEN_NAME ?>': '<?= csrf_token() ?>',
                        status,
                        notes: notes.value,
                    }),
                });

                const data = await res.json();
                msg.className = 'mb-3 text-sm px-3 py-2 rounded-lg';

                if (data.success) {
                    msg.classList.add('bg-green-100', 'text-green-700');
                    msg.textContent = data.message ?? 'Status updated!';
                    msg.classList.remove('hidden');
                    setTimeout(() => location.reload(), 800);
                } else {
                    msg.classList.add('bg-red-100', 'text-red-700');
                    msg.textContent = data.message ?? 'Update failed.';
                    msg.classList.remove('hidden');
                    buttons.forEach(b => b.disabled = false);
                }
            } catch {
                msg.className = 'mb-3 text-sm px-3 py-2 rounded-lg bg-red-100 text-red-700';
                msg.textContent = 'Network error. Please try again.';
                msg.classList.remove('hidden');
                buttons.forEach(b => b.disabled = false);
            }
        });
    });
})();
</script>