<?php
// app/views/admin/promos.php
/** @var array $promos @var int $total @var int $page @var int $totalPages */
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Promo Codes</h1>
        <p class="admin-page-sub"><?= $total ?> code<?= $total !== 1 ? 's' : '' ?> total</p>
    </div>
    <a href="<?= APP_URL ?>/admin/promos/create" class="btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Promo Code
    </a>
</div>

<?php if (Session::hasFlash('message')): ?>
    <?php $f = Session::getFlash('message'); ?>
    <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['text']) ?></div>
<?php endif; ?>

<div class="admin-card">
    <?php if (empty($promos)): ?>
        <div class="empty-state">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <p>No promo codes yet.</p>
            <a href="<?= APP_URL ?>/admin/promos/create" class="btn-primary btn-sm">Create your first promo</a>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Min Order</th>
                        <th>Uses</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($promos as $p): ?>
                    <tr>
                        <td>
                            <code class="promo-code-pill"><?= e($p['code']) ?></code>
                        </td>
                        <td><?= $p['type'] === 'percent' ? 'Percent (%)' : 'Fixed (₱)' ?></td>
                        <td>
                            <?php if ($p['type'] === 'percent'): ?>
                                <?= e($p['value']) ?>%
                            <?php else: ?>
                                ₱<?= number_format((float)$p['value'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= (float)$p['min_order'] > 0 ? '₱' . number_format((float)$p['min_order'], 2) : '—' ?>
                        </td>
                        <td>
                            <?= $p['uses'] ?>
                            <?= $p['max_uses'] !== null ? ' / ' . $p['max_uses'] : '' ?>
                        </td>
                        <td>
                            <?= $p['expires_at']
                                ? date('M d, Y', strtotime($p['expires_at']))
                                : '—' ?>
                        </td>
                        <td>
                            <?php
                                $expired = $p['expires_at'] && strtotime($p['expires_at']) < time();
                                $maxed   = $p['max_uses'] !== null && $p['uses'] >= $p['max_uses'];
                                if (!$p['is_active'] || $expired || $maxed):
                            ?>
                                <span class="badge badge-red">
                                    <?= !$p['is_active'] ? 'Inactive' : ($expired ? 'Expired' : 'Used up') ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-green">Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions-cell">
                            <a href="<?= APP_URL ?>/admin/promos/<?= $p['id'] ?>/edit" class="btn-icon" title="Edit">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button
                                class="btn-icon btn-icon-danger delete-promo-btn"
                                data-id="<?= $p['id'] ?>"
                                data-code="<?= e($p['code']) ?>"
                                title="Delete">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Delete confirm modal -->
<div id="deleteModal" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <h3>Delete Promo Code?</h3>
        <p>Are you sure you want to delete <strong id="deleteCodeLabel"></strong>? This cannot be undone.</p>
        <div class="modal-actions">
            <button id="cancelDelete" class="btn-secondary">Cancel</button>
            <button id="confirmDelete" class="btn-danger">Delete</button>
        </div>
    </div>
</div>

<style>
.promo-code-pill {
    background: var(--color-forest, #2d5a27);
    color: #fff;
    padding: 2px 10px;
    border-radius: 4px;
    font-size: 0.85rem;
    letter-spacing: 0.05em;
}
</style>

<script>
(function () {
    let pendingId = null;

    document.querySelectorAll('.delete-promo-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            pendingId = btn.dataset.id;
            document.getElementById('deleteCodeLabel').textContent = btn.dataset.code;
            document.getElementById('deleteModal').style.display = 'flex';
        });
    });

    document.getElementById('cancelDelete').addEventListener('click', () => {
        document.getElementById('deleteModal').style.display = 'none';
        pendingId = null;
    });

    document.getElementById('confirmDelete').addEventListener('click', async () => {
        if (!pendingId) return;

        const res  = await fetch(`<?= APP_URL ?>/admin/promos/${pendingId}/delete`, {
            method  : 'POST',
            headers : {
                'Content-Type'     : 'application/x-www-form-urlencoded',
                'X-Requested-With' : 'XMLHttpRequest',
            },
            body: `<?= CSRF_TOKEN_NAME ?>=<?= csrf_token() ?>`,
        });
        const data = await res.json();

        document.getElementById('deleteModal').style.display = 'none';

        if (data.success) {
            // Remove row from table
            const btn = document.querySelector(`.delete-promo-btn[data-id="${pendingId}"]`);
            if (btn) btn.closest('tr').remove();
            showToast(data.message || 'Deleted.', 'success');
        } else {
            showToast(data.message || 'Failed to delete.', 'error');
        }
        pendingId = null;
    });
})();
</script>