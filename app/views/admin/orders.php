<?php
// app/views/admin/orders.php
// Layout: admin
?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
            <p class="text-sm text-gray-500 mt-1" id="orders-count-text">
                <?= number_format($total) ?> order<?= $total !== 1 ? 's' : '' ?> found
            </p>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-4">
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
            // Build href preserving date filters
            $qs = [];
            if ($val)       $qs[] = 'status='    . urlencode($val);
            if ($dateFrom)  $qs[] = 'date_from=' . urlencode($dateFrom);
            if ($dateTo)    $qs[] = 'date_to='   . urlencode($dateTo);
            $href = APP_URL . '/admin/orders' . ($qs ? '?' . implode('&', $qs) : '');
        ?>
            <a href="<?= $href ?>"
               class="px-4 py-1.5 text-sm rounded-full border transition font-medium
                      <?= $active ? 'bg-pink-500 text-white border-pink-500' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Date Filter -->
    <form method="GET" action="<?= APP_URL ?>/admin/orders" class="flex flex-wrap items-end gap-3 mb-6">
        <?php if ($status): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
        <?php endif; ?>
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom ?? '') ?>"
                   class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-pink-300">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo ?? '') ?>"
                   class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-pink-300">
        </div>
        <button type="submit"
                class="px-4 py-1.5 text-sm bg-pink-500 text-white rounded-full hover:bg-pink-600 transition font-medium">
            Filter
        </button>
        <?php if (!empty($dateFrom) || !empty($dateTo)): ?>
            <a href="<?= APP_URL ?>/admin/orders<?= $status ? '?status=' . urlencode($status) : '' ?>"
               class="px-4 py-1.5 text-sm border border-gray-300 text-gray-600 rounded-full hover:bg-gray-50 transition">
                Clear
            </a>
        <?php endif; ?>
    </form>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="orders-tbody" class="divide-y divide-gray-100">

                    <?php if (empty($orders)): ?>
                        <tr id="empty-row">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
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

                                <!-- Payment method -->
                                <td class="px-6 py-4">
                                    <?php if (($order['payment_method'] ?? '') === 'online'): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                                            💳 Online
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            💵 COD
                                        </span>
                                    <?php endif; ?>
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
                    <?php
                    $qs = '';
                    if ($status)   $qs .= '&status='    . urlencode($status);
                    if ($dateFrom) $qs .= '&date_from=' . urlencode($dateFrom);
                    if ($dateTo)   $qs .= '&date_to='   . urlencode($dateTo);
                    ?>

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

<script>
(function () {
    if (typeof Pusher === 'undefined') return;

    const pusher = new Pusher('<?= defined("PUSHER_APP_KEY") ? PUSHER_APP_KEY : "" ?>', {
        cluster: '<?= defined("PUSHER_APP_CLUSTER") ? PUSHER_APP_CLUSTER : "ap1" ?>',
        authEndpoint: '<?= APP_URL ?>/pusher/auth',
    });

    const channel = pusher.subscribe('private-admin');

    // ── Reset nav badge to 0 — admin is on the orders page, so they've seen them ──
    const navBadge = document.getElementById('admin-orders-badge');
    if (navBadge) {
        navBadge.textContent = '0';
        navBadge.classList.add('hidden');
    }

    // ── Track live-injected order IDs to prevent duplicate rows ──
    const injectedIds = new Set();

    // ── new-order: inject a row at the BOTTOM of the table (first come, first serve) ──
    channel.bind('new-order', function (data) {
        if (injectedIds.has(data.order_id)) return;
        injectedIds.add(data.order_id);

        const tbody        = document.getElementById('orders-tbody');
        const emptyRow     = document.getElementById('empty-row');
        const statusFilter = new URLSearchParams(window.location.search).get('status') || '';

        if (emptyRow) emptyRow.remove();

        const orderStatus = data.order_status || 'confirmed';
        const tabWillShow = statusFilter === '' || statusFilter === orderStatus;

        bumpOrderCount();

        if (!tabWillShow || !tbody) return;

        const now     = new Date();
        const dateStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
        const amount  = parseFloat(data.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });

        const statusColors = {
            confirmed:  'bg-blue-100 text-blue-700',
            pending:    'bg-yellow-100 text-yellow-700',
            processing: 'bg-indigo-100 text-indigo-700',
            ready:      'bg-teal-100 text-teal-700',
            delivered:  'bg-green-100 text-green-700',
            cancelled:  'bg-red-100 text-red-700',
        };
        const badgeClass = statusColors[orderStatus] ?? 'bg-gray-100 text-gray-600';
        const badgeLabel = orderStatus.charAt(0).toUpperCase() + orderStatus.slice(1);

        const payMethod = data.payment_method === 'online'
            ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600">💳 Online</span>'
            : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">💵 COD</span>';

        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition bg-green-50';
        row.innerHTML = `
            <td class="px-6 py-4">
                <div class="font-medium text-gray-900">${escHtml(data.order_number || '')}</div>
            </td>
            <td class="px-6 py-4">
                <div class="font-medium text-gray-800">${escHtml(data.customer || 'Customer')}</div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium ${badgeClass}">${escHtml(badgeLabel)}</span>
            </td>
            <td class="px-6 py-4">${payMethod}</td>
            <td class="px-6 py-4 font-medium text-gray-900">₱${amount}</td>
            <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                ${dateStr}
                <div class="text-gray-400">${timeStr}</div>
            </td>
            <td class="px-6 py-4 text-right">
                <a href="<?= APP_URL ?>/admin/orders/${escHtml(String(data.order_id))}"
                   class="text-xs text-pink-500 hover:text-pink-700 font-medium transition">
                    View →
                </a>
            </td>
        `;

        // Append to bottom — first come, first serve order
        tbody.appendChild(row);

        // Fade the green highlight out after 4 seconds
        setTimeout(() => row.classList.remove('bg-green-50'), 4000);
    });

    // ── order-status-changed: update the badge cell of an existing row ────────
    channel.bind('order-status-changed', function (data) {
        const link = document.querySelector(
            `a[href$="/admin/orders/${data.order_id}"]`
        );
        if (!link) return;

        const row       = link.closest('tr');
        const badgeCell = row ? row.querySelector('td:nth-child(3)') : null;
        if (!badgeCell) return;

        const statusColors = {
            confirmed:  'bg-blue-100 text-blue-700',
            pending:    'bg-yellow-100 text-yellow-700',
            processing: 'bg-indigo-100 text-indigo-700',
            ready:      'bg-teal-100 text-teal-700',
            delivered:  'bg-green-100 text-green-700',
            cancelled:  'bg-red-100 text-red-700',
        };
        const cls   = statusColors[data.new_status] ?? 'bg-gray-100 text-gray-600';
        const label = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1);

        badgeCell.innerHTML = `<span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${escHtml(label)}</span>`;

        row.classList.add('bg-blue-50');
        setTimeout(() => row.classList.remove('bg-blue-50'), 3000);
    });

    // ── Helpers ───────────────────────────────────────────────────────────────
    function bumpOrderCount() {
        const el = document.getElementById('orders-count-text');
        if (!el) return;
        const match = el.textContent.match(/(\d[\d,]*)/);
        if (!match) return;
        const n = parseInt(match[1].replace(/,/g, ''), 10) + 1;
        el.textContent = n.toLocaleString() + ' order' + (n !== 1 ? 's' : '') + ' found';
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
})();
</script>