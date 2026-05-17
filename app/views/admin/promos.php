<?php
// app/views/admin/promos.php
/** @var array $promos @var int $total @var int $page @var int $totalPages */
?>

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-1">Catalog</p>
        <h1 class="text-2xl text-forest" style="font-family: var(--font-display);">Promo Codes</h1>
        <p class="text-sm text-muted mt-0.5"><?= $total ?> code<?= $total !== 1 ? 's' : '' ?> total</p>
    </div>
    <a href="<?= APP_URL ?>/admin/promos/create"
       class="inline-flex items-center gap-2 bg-forest hover:bg-pine text-white text-sm font-medium px-5 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Promo Code
    </a>
</div>

<?php if (Session::hasFlash('message')): ?>
    <?php $f = Session::getFlash('message'); ?>
    <?php
        $cls = match($f['type'] ?? 'info') {
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'error'   => 'bg-red-50 border-red-200 text-red-800',
            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            default   => 'bg-blue-50 border-blue-200 text-blue-800',
        };
        $icon = match($f['type'] ?? 'info') {
            'success' => '✓', 'error' => '✕', 'warning' => '!', default => 'i',
        };
    ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border mb-5 text-sm <?= $cls ?>">
        <span class="font-bold w-4 text-center"><?= $icon ?></span>
        <span><?= e($f['text'] ?? $f['message'] ?? '') ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-50 hover:opacity-100 transition-opacity">✕</button>
    </div>
<?php endif; ?>

<!-- Card -->
<div class="bg-white border border-border rounded-2xl overflow-hidden">

    <?php if (empty($promos)): ?>
        <!-- Empty state -->
        <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
            <div class="w-16 h-16 rounded-2xl bg-cream border border-border flex items-center justify-center mb-4">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted">
                    <path d="M7 7h.01M7 3h5l9 9a2 2 0 010 2.828l-5.172 5.172a2 2 0 01-2.828 0L4 11V3h3z"/>
                </svg>
            </div>
            <p class="text-base font-semibold text-text mb-1">No promo codes yet</p>
            <p class="text-sm text-muted mb-5">Create your first promo code to offer discounts at checkout.</p>
            <a href="<?= APP_URL ?>/admin/promos/create"
               class="inline-flex items-center gap-2 bg-forest hover:bg-pine text-white text-sm font-medium px-5 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Create your first promo
            </a>
        </div>

    <?php else: ?>
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-cream/60">
                        <th class="text-left text-xs font-semibold text-muted tracking-widest uppercase px-6 py-3.5">Code</th>
                        <th class="text-left text-xs font-semibold text-muted tracking-widest uppercase px-4 py-3.5">Type</th>
                        <th class="text-left text-xs font-semibold text-muted tracking-widest uppercase px-4 py-3.5">Value</th>
                        <th class="text-left text-xs font-semibold text-muted tracking-widest uppercase px-4 py-3.5">Min Order</th>
                        <th class="text-left text-xs font-semibold text-muted tracking-widest uppercase px-4 py-3.5">Uses</th>
                        <th class="text-left text-xs font-semibold text-muted tracking-widest uppercase px-4 py-3.5">Expires</th>
                        <th class="text-left text-xs font-semibold text-muted tracking-widest uppercase px-4 py-3.5">Status</th>
                        <th class="text-right text-xs font-semibold text-muted tracking-widest uppercase px-6 py-3.5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($promos as $p): ?>
                    <?php
                        $expired = $p['expires_at'] && strtotime($p['expires_at']) < time();
                        $maxed   = $p['max_uses'] !== null && $p['uses'] >= $p['max_uses'];
                        $isActive = $p['is_active'] && !$expired && !$maxed;
                    ?>
                    <tr class="hover:bg-cream/40 transition-colors">
                        <!-- Code -->
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5">
                                <code class="bg-forest text-white text-xs font-mono font-bold tracking-widest px-3 py-1 rounded-lg">
                                    <?= e($p['code']) ?>
                                </code>
                            </span>
                        </td>
                        <!-- Type -->
                        <td class="px-4 py-4 text-muted">
                            <?= $p['type'] === 'percent' ? 'Percentage' : 'Fixed' ?>
                        </td>
                        <!-- Value -->
                        <td class="px-4 py-4 font-semibold text-text">
                            <?php if ($p['type'] === 'percent'): ?>
                                <?= e($p['value']) ?>%
                            <?php else: ?>
                                ₱<?= number_format((float)$p['value'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <!-- Min Order -->
                        <td class="px-4 py-4 text-muted">
                            <?= (float)$p['min_order'] > 0 ? '₱' . number_format((float)$p['min_order'], 2) : '—' ?>
                        </td>
                        <!-- Uses -->
                        <td class="px-4 py-4">
                            <span class="text-text font-medium"><?= $p['uses'] ?></span>
                            <?php if ($p['max_uses'] !== null): ?>
                                <span class="text-muted"> / <?= $p['max_uses'] ?></span>
                                <!-- Usage bar -->
                                <div class="w-16 h-1 bg-border rounded-full mt-1 overflow-hidden">
                                    <div class="h-full bg-forest rounded-full transition-all"
                                         style="width: <?= min(100, round($p['uses'] / $p['max_uses'] * 100)) ?>%"></div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted text-xs ml-1">/ ∞</span>
                            <?php endif; ?>
                        </td>
                        <!-- Expires -->
                        <td class="px-4 py-4 text-muted text-xs">
                            <?php if ($p['expires_at']): ?>
                                <span class="<?= $expired ? 'text-red-500' : '' ?>">
                                    <?= date('M d, Y', strtotime($p['expires_at'])) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted/60">No expiry</span>
                            <?php endif; ?>
                        </td>
                        <!-- Status -->
                        <td class="px-4 py-4">
                            <?php if ($isActive): ?>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                    <?= !$p['is_active'] ? 'Inactive' : ($expired ? 'Expired' : 'Used up') ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <a href="<?= APP_URL ?>/admin/promos/<?= $p['id'] ?>/edit"
                                   title="Edit"
                                   class="p-2 rounded-lg text-muted hover:text-forest hover:bg-cream border border-transparent hover:border-border transition-all">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <button
                                    class="p-2 rounded-lg text-muted hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 transition-all delete-promo-btn"
                                    data-id="<?= $p['id'] ?>"
                                    data-code="<?= e($p['code']) ?>"
                                    title="Delete">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6M14 11v6M9 6V4h6v2"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-1.5 px-6 py-4 border-t border-border">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-all
                          <?= $i === $page
                              ? 'bg-forest text-white font-semibold'
                              : 'text-muted hover:bg-cream hover:text-forest border border-transparent hover:border-border' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<!-- ── Delete confirm modal ── -->
<div id="deleteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl border border-border w-full max-w-sm mx-4 p-6">
        <div class="w-12 h-12 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center mb-4">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-text mb-1" style="font-family: var(--font-display);">Delete Promo Code?</h3>
        <p class="text-sm text-muted mb-5">
            Are you sure you want to delete <strong id="deleteCodeLabel" class="text-text font-mono"></strong>?
            This cannot be undone.
        </p>
        <div class="flex gap-3">
            <button id="cancelDelete"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-border text-sm text-muted hover:bg-cream hover:text-text transition-all">
                Cancel
            </button>
            <button id="confirmDelete"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition-all">
                Delete
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    let pendingId = null;
    const modal = document.getElementById('deleteModal');

    document.querySelectorAll('.delete-promo-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            pendingId = btn.dataset.id;
            document.getElementById('deleteCodeLabel').textContent = btn.dataset.code;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        pendingId = null;
    }

    document.getElementById('cancelDelete').addEventListener('click', closeModal);

    // Close on backdrop click
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    document.getElementById('confirmDelete').addEventListener('click', async () => {
        if (!pendingId) return;

        const btn = document.getElementById('confirmDelete');
        btn.disabled    = true;
        btn.textContent = 'Deleting...';

        try {
            const res  = await fetch(`<?= APP_URL ?>/admin/promos/${pendingId}/delete`, {
                method  : 'POST',
                headers : {
                    'Content-Type'     : 'application/x-www-form-urlencoded',
                    'X-Requested-With' : 'XMLHttpRequest',
                },
                body: `<?= CSRF_TOKEN_NAME ?>=<?= csrf_token() ?>`,
            });
            const data = await res.json();

            closeModal();

            if (data.success) {
                const row = document.querySelector(`.delete-promo-btn[data-id="${pendingId}"]`);
                if (row) row.closest('tr').remove();
                showToast(data.message || 'Deleted.', 'success');

                // Update total count
                const sub = document.querySelector('p.text-sm.text-muted');
                if (sub) {
                    const current = parseInt(sub.textContent) || 0;
                    const next = Math.max(0, current - 1);
                    sub.textContent = next + ' code' + (next !== 1 ? 's' : '') + ' total';
                }
            } else {
                showToast(data.message || 'Failed to delete.', 'error');
            }
        } catch (err) {
            closeModal();
            showToast('Network error. Please try again.', 'error');
        }

        btn.disabled    = false;
        btn.textContent = 'Delete';
        pendingId = null;
    });
})();
</script>