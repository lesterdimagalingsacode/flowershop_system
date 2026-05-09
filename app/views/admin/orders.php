<?php
// app/views/admin/orders.php
// Layout: admin
?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
            <p class="text-sm text-gray-500 mt-1">
                <?= number_format($total) ?> order<?= $total !== 1 ? 's' : '' ?> found
            </p>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-6">
        <?php
        $statuses = [
            ''           => 'All',
            'pending'    => 'Pending',
            'confirmed'  => 'Confirmed',
            'processing' => 'Processing',
            'ready'      => 'Ready',
            'delivered'  => 'Delivered',
            'cancelled'  => 'Cancelled',
        ];
        foreach ($statuses as $val => $label):
            $active = $status === $val;
        ?>
            <a href="<?= APP_URL ?>/admin/orders<?= $val ? '?status=' . $val : '' ?>"
               class="px-4 py-1.5 text-sm rounded-full border transition font-medium
                      <?= $active ? 'bg-pink-500 text-white border-pink-500' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2"/>
                                </svg>
                                No orders found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 transition">

                                <!-- Order number -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($order['order_number']) ?></div>
                                    <?php if (!empty($order['promo_code'])): ?>
                                        <div class="text-xs text-pink-500 mt-0.5">🏷 <?= htmlspecialchars($order['promo_code']) ?></div>
                                    <?php endif; ?>
                                </td>

                                <!-- Customer -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">
                                        <?= htmlspecialchars($order['customer_name'] ?? $order['first_name'] ?? 'Unknown') ?>
                                    </div>
                                    <div class="text-xs text-gray-400"><?= htmlspecialchars($order['customer_email'] ?? $order['email'] ?? '') ?></div>
                                </td>

                                <!-- Status badge -->
                                <td class="px-6 py-4">
                                    <?= orderStatusBadge($order['status']) ?>
                                </td>

                                <!-- Total -->
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    ₱<?= number_format((float)$order['total_amount'], 2) ?>
                                    <?php if ((float)($order['discount_amount'] ?? 0) > 0): ?>
                                        <div class="text-xs text-green-600">-₱<?= number_format((float)$order['discount_amount'], 2) ?> off</div>
                                    <?php endif; ?>
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                    <?= date('M j, Y', strtotime($order['created_at'])) ?>
                                    <div class="text-gray-400"><?= date('g:i A', strtotime($order['created_at'])) ?></div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <a href="<?= APP_URL ?>/admin/orders/<?= $order['id'] ?>"
                                       class="text-xs text-pink-500 hover:text-pink-700 font-medium transition">
                                        View →
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Page <?= $page ?> of <?= $totalPages ?>
                </p>
                <div class="flex gap-1">
                    <?php $qs = $status ? '&status=' . urlencode($status) : ''; ?>

                    <?php if ($page > 1): ?>
                        <a href="<?= APP_URL ?>/admin/orders?page=<?= $page - 1 . $qs ?>" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition">← Prev</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="<?= APP_URL ?>/admin/orders?page=<?= $i . $qs ?>"
                           class="px-3 py-1 text-sm border rounded-md transition <?= $i === $page ? 'bg-pink-500 text-white border-pink-500' : 'border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= APP_URL ?>/admin/orders?page=<?= $page + 1 . $qs ?>" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition">Next →</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
function orderStatusBadge(string $status): string {
    $map = [
        'pending'    => 'bg-yellow-100 text-yellow-700',
        'confirmed'  => 'bg-blue-100 text-blue-700',
        'processing' => 'bg-indigo-100 text-indigo-700',
        'ready'      => 'bg-teal-100 text-teal-700',
        'delivered'  => 'bg-green-100 text-green-700',
        'cancelled'  => 'bg-red-100 text-red-700',
    ];
    $class = $map[$status] ?? 'bg-gray-100 text-gray-600';
    return "<span class=\"inline-block px-2 py-0.5 rounded-full text-xs font-medium {$class}\">" . ucfirst(htmlspecialchars($status)) . "</span>";
}
?>