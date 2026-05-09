<?php
// app/views/admin/promo-form.php
/** @var array|null $promo */
$isEdit   = $promo && isset($promo['id']);
$action   = $isEdit
    ? APP_URL . '/admin/promos/' . $promo['id'] . '/update'
    : APP_URL . '/admin/promos/store';
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title"><?= $isEdit ? 'Edit Promo Code' : 'New Promo Code' ?></h1>
        <p class="admin-page-sub">
            <a href="<?= APP_URL ?>/admin/promos">← Back to Promo Codes</a>
        </p>
    </div>
</div>

<?php if (Session::hasFlash('message')): ?>
    <?php $f = Session::getFlash('message'); ?>
    <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['text']) ?></div>
<?php endif; ?>

<div class="admin-card" style="max-width:640px">
    <form method="POST" action="<?= $action ?>">
        <?= csrf_field() ?>

        <!-- Code -->
        <div class="form-group">
            <label class="form-label" for="code">Promo Code <span class="required">*</span></label>
            <input
                type="text"
                id="code"
                name="code"
                class="form-control"
                value="<?= e($promo['code'] ?? '') ?>"
                placeholder="e.g. SUMMER20"
                maxlength="50"
                required
                style="text-transform:uppercase"
                oninput="this.value=this.value.toUpperCase()"
            >
            <span class="form-hint">Letters, numbers, hyphens, underscores only.</span>
        </div>

        <!-- Type + Value -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="type">Discount Type <span class="required">*</span></label>
                <select id="type" name="type" class="form-control" onchange="toggleValueHint()">
                    <option value="percent" <?= ($promo['type'] ?? 'percent') === 'percent' ? 'selected' : '' ?>>Percentage (%)</option>
                    <option value="fixed"   <?= ($promo['type'] ?? '') === 'fixed'   ? 'selected' : '' ?>>Fixed Amount (₱)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="value">Value <span class="required">*</span></label>
                <div class="input-prefix-wrap">
                    <span id="valuePrefix" class="input-prefix">%</span>
                    <input
                        type="number"
                        id="value"
                        name="value"
                        class="form-control with-prefix"
                        value="<?= e($promo['value'] ?? '') ?>"
                        min="0.01"
                        step="0.01"
                        required
                    >
                </div>
            </div>
        </div>

        <!-- Min order -->
        <div class="form-group">
            <label class="form-label" for="min_order">Minimum Order Amount</label>
            <div class="input-prefix-wrap">
                <span class="input-prefix">₱</span>
                <input
                    type="number"
                    id="min_order"
                    name="min_order"
                    class="form-control with-prefix"
                    value="<?= e($promo['min_order'] ?? '0') ?>"
                    min="0"
                    step="0.01"
                >
            </div>
            <span class="form-hint">Set to 0 for no minimum.</span>
        </div>

        <!-- Max uses -->
        <div class="form-group">
            <label class="form-label" for="max_uses">Max Uses</label>
            <input
                type="number"
                id="max_uses"
                name="max_uses"
                class="form-control"
                value="<?= e($promo['max_uses'] ?? '') ?>"
                min="1"
                placeholder="Leave blank for unlimited"
            >
        </div>

        <!-- Expires at -->
        <div class="form-group">
            <label class="form-label" for="expires_at">Expiry Date</label>
            <input
                type="datetime-local"
                id="expires_at"
                name="expires_at"
                class="form-control"
                value="<?= e(isset($promo['expires_at']) && $promo['expires_at']
                    ? date('Y-m-d\TH:i', strtotime($promo['expires_at']))
                    : '') ?>"
            >
            <span class="form-hint">Leave blank for no expiry.</span>
        </div>

        <!-- Active toggle -->
        <div class="form-group">
            <label class="form-label">Status</label>
            <label class="toggle-label">
                <input type="hidden"   name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                    <?= ($promo['is_active'] ?? 1) ? 'checked' : '' ?>>
                <span class="toggle-track"></span>
                <span class="toggle-text">Active</span>
            </label>
        </div>

        <div class="form-actions">
            <a href="<?= APP_URL ?>/admin/promos" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <?= $isEdit ? 'Save Changes' : 'Create Promo Code' ?>
            </button>
        </div>
    </form>
</div>

<style>
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.input-prefix-wrap { position:relative; }
.input-prefix {
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    color:var(--color-muted, #888); font-size:.9rem; pointer-events:none;
}
.form-control.with-prefix { padding-left:28px; }
</style>

<script>
function toggleValueHint() {
    const type   = document.getElementById('type').value;
    const prefix = document.getElementById('valuePrefix');
    prefix.textContent = type === 'percent' ? '%' : '₱';

    const valueInput = document.getElementById('value');
    if (type === 'percent') {
        valueInput.max  = '100';
        valueInput.step = '1';
    } else {
        valueInput.removeAttribute('max');
        valueInput.step = '0.01';
    }
}
toggleValueHint(); // run on load
</script>