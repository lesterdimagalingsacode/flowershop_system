<?php
// app/views/admin/promo-form.php
/** @var array|null $promo */
$isEdit = $promo && isset($promo['id']);
$action = $isEdit
    ? APP_URL . '/admin/promos/' . $promo['id'] . '/update'
    : APP_URL . '/admin/promos/store';
?>

<div class="max-w-3xl space-y-5">

    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="<?= APP_URL ?>/admin/promos"
           class="p-2 rounded-xl text-muted hover:text-forest hover:bg-white border border-transparent hover:border-border transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-0.5">Promotions</p>
            <h2 class="text-xl font-bold text-forest" style="font-family: var(--font-display);">
                <?= $isEdit ? 'Edit Promo Code' : 'New Promo Code' ?>
            </h2>
        </div>
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
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm <?= $cls ?>">
            <span class="font-bold w-4 text-center"><?= $icon ?></span>
            <span><?= e($f['text'] ?? $f['message'] ?? '') ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $action ?>" class="space-y-5">
        <?= csrf_field() ?>

        <!-- Code & Type -->
        <div class="bg-white border border-border rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-text border-b border-border pb-3">Promo Details</h3>

            <!-- Code -->
            <div>
                <label class="block text-xs font-medium text-muted mb-1.5">
                    Promo Code <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="code"
                    required
                    maxlength="50"
                    value="<?= e($promo['code'] ?? '') ?>"
                    placeholder="e.g. SUMMER20"
                    oninput="this.value=this.value.toUpperCase()"
                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream tracking-widest font-mono uppercase"
                >
                <p class="text-[0.65rem] text-muted mt-1">Letters, numbers, hyphens, and underscores only.</p>
            </div>

            <!-- Type + Value -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">
                        Discount Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            onchange="toggleValuePrefix()"
                            class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                        <option value="percent" <?= ($promo['type'] ?? 'percent') === 'percent' ? 'selected' : '' ?>>
                            Percentage (%)
                        </option>
                        <option value="fixed" <?= ($promo['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>
                            Fixed Amount (₱)
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">
                        Value <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span id="valuePrefix"
                              class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted pointer-events-none font-medium">
                            <?= ($promo['type'] ?? 'percent') === 'fixed' ? '₱' : '%' ?>
                        </span>
                        <input
                            type="number"
                            name="value"
                            id="value"
                            required
                            min="0.01"
                            step="0.01"
                            value="<?= e($promo['value'] ?? '') ?>"
                            placeholder="0"
                            class="w-full pl-8 pr-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Constraints -->
        <div class="bg-white border border-border rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-text border-b border-border pb-3">Constraints</h3>

            <div class="grid grid-cols-2 gap-4">
                <!-- Min order -->
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Minimum Order Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted pointer-events-none font-medium">₱</span>
                        <input
                            type="number"
                            name="min_order"
                            min="0"
                            step="0.01"
                            value="<?= e($promo['min_order'] ?? '0') ?>"
                            class="w-full pl-8 pr-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream"
                        >
                    </div>
                    <p class="text-[0.65rem] text-muted mt-1">Set to 0 for no minimum.</p>
                </div>

                <!-- Max uses -->
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Max Uses</label>
                    <input
                        type="number"
                        name="max_uses"
                        min="1"
                        value="<?= e($promo['max_uses'] ?? '') ?>"
                        placeholder="Unlimited"
                        class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream"
                    >
                    <p class="text-[0.65rem] text-muted mt-1">Leave blank for unlimited uses.</p>
                </div>
            </div>

            <!-- Expires at -->
            <div>
                <label class="block text-xs font-medium text-muted mb-1.5">Expiry Date</label>
                <input
                    type="datetime-local"
                    name="expires_at"
                    value="<?= e(isset($promo['expires_at']) && $promo['expires_at']
                        ? date('Y-m-d\TH:i', strtotime($promo['expires_at']))
                        : '') ?>"
                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream"
                >
                <p class="text-[0.65rem] text-muted mt-1">Leave blank for no expiry.</p>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white border border-border rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-text border-b border-border pb-3 mb-4">Status</h3>

            <!-- Hidden fallback — must be BEFORE the checkbox -->
            <input type="hidden" name="is_active" value="0">

            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           <?= ($promo['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="sr-only peer">
                    <div class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-forest transition-colors"></div>
                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-text">Active</p>
                    <p class="text-xs text-muted">When off, this promo code cannot be applied at checkout.</p>
                </div>
            </label>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-forest hover:bg-pine text-white text-sm font-semibold px-7 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                <?= $isEdit ? 'Save Changes' : 'Create Promo Code' ?>
            </button>
            <a href="<?= APP_URL ?>/admin/promos"
               class="text-sm text-muted hover:text-forest transition-colors px-4 py-2.5">
                Cancel
            </a>
        </div>

    </form>
</div>

<script>
function toggleValuePrefix() {
    const type   = document.getElementById('type').value;
    const prefix = document.getElementById('valuePrefix');
    const input  = document.getElementById('value');

    prefix.textContent = type === 'fixed' ? '₱' : '%';

    if (type === 'percent') {
        input.max  = '100';
        input.step = '1';
    } else {
        input.removeAttribute('max');
        input.step = '0.01';
    }
}

// Run on load so the prefix matches the current type on edit
toggleValuePrefix();
</script>