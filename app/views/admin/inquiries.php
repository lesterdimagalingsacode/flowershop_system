<?php
// app/views/admin/inquiries.php
// Layout: admin
?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                Customer Inquiries
                <?php if ($unread > 0): ?>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-pink-500 text-white">
                        <?= $unread ?>
                    </span>
                <?php endif; ?>
            </h1>
            <p class="text-sm text-gray-500 mt-1"><?= number_format($total) ?> inquiry<?= $total !== 1 ? 'ies' : 'y' ?> found</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="/admin/inquiries" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">

            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search by name or email…"
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                />
            </div>

            <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
                <option value="">All Status</option>
                <option value="open"     <?= $status === 'open'     ? 'selected' : '' ?>>Open</option>
                <option value="resolved" <?= $status === 'resolved' ? 'selected' : '' ?>>Resolved</option>
            </select>

            <select name="is_read" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
                <option value="">All Messages</option>
                <option value="0" <?= $isRead === '0' ? 'selected' : '' ?>>Unread</option>
                <option value="1" <?= $isRead === '1' ? 'selected' : '' ?>>Read</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition">Filter</button>

            <?php if ($search || $status || $isRead !== ''): ?>
                <a href="/admin/inquiries" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/>
                                </svg>
                                No inquiries found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <tr class="hover:bg-gray-50 transition <?= ! $msg['is_read'] ? 'bg-pink-50/40' : '' ?>">

                                <!-- Customer -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 flex items-center gap-2">
                                        <?php if (! $msg['is_read']): ?>
                                            <span class="w-2 h-2 rounded-full bg-pink-500 flex-shrink-0"></span>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($msg['customer_name']) ?>
                                    </div>
                                    <div class="text-gray-400 text-xs"><?= htmlspecialchars($msg['customer_email']) ?></div>
                                </td>

                                <!-- Message preview -->
                                <td class="px-6 py-4 max-w-xs">
                                    <p class="text-gray-600 truncate">
                                        <?= htmlspecialchars(mb_substr($msg['message'], 0, 80)) ?><?= mb_strlen($msg['message']) > 80 ? '…' : '' ?>
                                    </p>
                                    <?php if ($msg['reply_count'] > 0): ?>
                                        <span class="text-xs text-gray-400"><?= $msg['reply_count'] ?> reply<?= $msg['reply_count'] > 1 ? 'ies' : '' ?></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Order ref -->
                                <td class="px-6 py-4 text-gray-500">
                                    <?= $msg['order_ref'] ? '<span class="font-mono">#' . $msg['order_ref'] . '</span>' : '—' ?>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    <?php if ($msg['status'] === 'resolved'): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Resolved
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Open
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                    <?= date('M j, Y', strtotime($msg['created_at'])) ?>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 text-right">
                                    <a href="/admin/inquiries/<?= $msg['id'] ?>"
                                       class="text-sm text-pink-500 hover:text-pink-700 font-medium transition">
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
                    Showing <?= (($page - 1) * $perPage) + 1 ?>–<?= min($page * $perPage, $total) ?> of <?= number_format($total) ?>
                </p>
                <div class="flex gap-1">
                    <?php
                    $q = http_build_query(array_filter(['search' => $search, 'status' => $status, 'is_read' => $isRead]));
                    $q = $q ? '&' . $q : '';
                    ?>
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 . $q ?>" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition">← Prev</a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?= $i . $q ?>"
                           class="px-3 py-1 text-sm border rounded-md transition <?= $i === $page ? 'bg-pink-500 text-white border-pink-500' : 'border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 . $q ?>" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition">Next →</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>