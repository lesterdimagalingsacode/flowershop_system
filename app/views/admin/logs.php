<?php $title = 'Activity Logs'; ?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-1">System</p>
            <h1 class="text-2xl text-text" style="font-family: var(--font-display);">Activity Logs</h1>
            <p class="text-sm text-muted mt-1"><?= number_format($total) ?> total entries</p>
        </div>
    </div>

    <!-- ── Filters ── -->
    <form method="GET" action="<?= APP_URL ?>/admin/logs"
          class="bg-white border border-border rounded-2xl p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div>
                <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">Action</label>
                <input type="text" name="action"
                       value="<?= e($filters['action']) ?>"
                       placeholder="e.g. order.status_updated"
                       class="w-full bg-cream border border-border rounded-xl px-3 py-2 text-sm text-text
                              focus:outline-none focus:border-forest focus:ring-1 focus:ring-forest/20 transition">
            </div>

            <div>
                <label class="block text-xs font-medium text-text tracking-widests uppercase mb-2">Model</label>
                <select name="model"
                        class="w-full bg-cream border border-border rounded-xl px-3 py-2 text-sm text-text
                               focus:outline-none focus:border-forest transition">
                    <option value="">All</option>
                    <?php foreach ($models as $m): ?>
                    <option value="<?= e($m) ?>" <?= $filters['model'] === $m ? 'selected' : '' ?>>
                        <?= e($m) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-text tracking-widests uppercase mb-2">From</label>
                <input type="date" name="date_from"
                       value="<?= e($filters['dateFrom']) ?>"
                       class="w-full bg-cream border border-border rounded-xl px-3 py-2 text-sm text-text
                              focus:outline-none focus:border-forest transition">
            </div>

            <div>
                <label class="block text-xs font-medium text-text tracking-widests uppercase mb-2">To</label>
                <input type="date" name="date_to"
                       value="<?= e($filters['dateTo']) ?>"
                       class="w-full bg-cream border border-border rounded-xl px-3 py-2 text-sm text-text
                              focus:outline-none focus:border-forest transition">
            </div>

        </div>
        <div class="flex gap-3 mt-4">
            <button type="submit"
                    class="bg-forest hover:bg-pine text-white text-xs font-medium px-5 py-2 rounded-full transition">
                Filter
            </button>
            <a href="<?= APP_URL ?>/admin/logs"
               class="bg-white border border-border hover:border-forest text-text text-xs font-medium px-5 py-2 rounded-full transition">
                Clear
            </a>
        </div>
    </form>

    <!-- ── Logs table ── -->
    <div class="bg-white border border-border rounded-2xl overflow-hidden">
        <?php if (empty($logs)): ?>
        <div class="text-center py-16">
            <div class="text-4xl mb-3">📋</div>
            <p class="text-muted text-sm">No logs found.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-cream">
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted uppercase tracking-wider">Time</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted uppercase tracking-wider">User</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted uppercase tracking-wider">Action</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted uppercase tracking-wider">Model</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted uppercase tracking-wider">Changes</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted uppercase tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($logs as $log): ?>
                    <?php
                        $actionColors = [
                            'user.login'            => 'bg-blue-50 text-blue-700',
                            'user.logout'           => 'bg-gray-50 text-gray-600',
                            'user.registered'       => 'bg-green-50 text-green-700',
                            'user.profile_updated'  => 'bg-yellow-50 text-yellow-700',
                            'user.password_changed' => 'bg-orange-50 text-orange-700',
                            'order.status_updated'  => 'bg-purple-50 text-purple-700',
                            'order.cancelled'       => 'bg-red-50 text-red-700',
                            'product.created'       => 'bg-green-50 text-green-700',
                            'product.updated'       => 'bg-yellow-50 text-yellow-700',
                            'product.deleted'       => 'bg-red-50 text-red-700',
                            'product.stock_updated' => 'bg-teal-50 text-teal-700',
                            'backup.downloaded'     => 'bg-forest/10 text-forest',
                            'backup.restored'       => 'bg-amber-50 text-amber-700',
                        ];
                        $cls = $actionColors[$log['action']] ?? 'bg-gray-50 text-gray-600';

                        $oldVals = $log['old_values'] ? json_decode($log['old_values'], true) : null;
                        $newVals = $log['new_values'] ? json_decode($log['new_values'], true) : null;
                    ?>
                    <tr class="hover:bg-cream/50 transition-colors">
                        <!-- Time -->
                        <td class="px-5 py-3.5 text-xs text-muted whitespace-nowrap">
                            <?= date('M j, Y', strtotime($log['created_at'])) ?>
                            <span class="block text-subtle">
                                <?= date('g:i A', strtotime($log['created_at'])) ?>
                            </span>
                        </td>

                        <!-- User -->
                        <td class="px-5 py-3.5">
                            <?php if ($log['user_name']): ?>
                            <p class="font-medium text-text text-xs"><?= e(trim($log['user_name'])) ?></p>
                            <p class="text-[0.65rem] text-muted"><?= e($log['user_email'] ?? '') ?></p>
                            <span class="text-[0.6rem] text-muted capitalize"><?= e($log['user_role'] ?? '') ?></span>
                            <?php else: ?>
                            <span class="text-xs text-muted italic">System</span>
                            <?php endif; ?>
                        </td>

                        <!-- Action -->
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.65rem] font-medium <?= $cls ?>">
                                <?= e($log['action']) ?>
                            </span>
                        </td>

                        <!-- Model -->
                        <td class="px-5 py-3.5 text-xs text-muted">
                            <?php if ($log['model']): ?>
                            <span class="font-medium text-text"><?= e($log['model']) ?></span>
                            <?php if ($log['model_id']): ?>
                            <span class="text-muted"> #<?= $log['model_id'] ?></span>
                            <?php endif; ?>
                            <?php else: ?>
                            —
                            <?php endif; ?>
                        </td>

                        <!-- Changes -->
                        <td class="px-5 py-3.5 text-xs max-w-xs">
                            <?php if ($oldVals || $newVals): ?>
                            <div class="space-y-1">
                                <?php if ($oldVals): ?>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($oldVals as $k => $v): ?>
                                    <span class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[0.6rem]">
                                        <?= e($k) ?>: <?= e(is_array($v) ? json_encode($v) : $v) ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($newVals): ?>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($newVals as $k => $v): ?>
                                    <span class="bg-green-50 text-green-600 px-1.5 py-0.5 rounded text-[0.6rem]">
                                        <?= e($k) ?>: <?= e(is_array($v) ? json_encode($v) : $v) ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- IP -->
                        <td class="px-5 py-3.5 text-xs text-muted font-mono">
                            <?= e($log['ip_address'] ?? '—') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="px-5 py-4 border-t border-border flex items-center justify-between">
            <p class="text-xs text-muted">
                Page <?= $page ?> of <?= $totalPages ?>
            </p>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&action=<?= urlencode($filters['action']) ?>&model=<?= urlencode($filters['model']) ?>&date_from=<?= urlencode($filters['dateFrom']) ?>&date_to=<?= urlencode($filters['dateTo']) ?>"
                   class="text-xs px-3 py-1.5 border border-border rounded-full hover:border-forest text-muted hover:text-forest transition">
                    ← Prev
                </a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&action=<?= urlencode($filters['action']) ?>&model=<?= urlencode($filters['model']) ?>&date_from=<?= urlencode($filters['dateFrom']) ?>&date_to=<?= urlencode($filters['dateTo']) ?>"
                   class="text-xs px-3 py-1.5 border border-border rounded-full hover:border-forest text-muted hover:text-forest transition">
                    Next →
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

</div>