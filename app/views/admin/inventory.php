<?php $title = 'Inventory'; ?>

<div class="space-y-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-0.5">Catalog</p>
            <h2 class="text-xl font-bold text-forest" style="font-family: var(--font-display);">Inventory</h2>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="<?= APP_URL ?>/admin/inventory"
          class="bg-white border border-border rounded-2xl px-5 py-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-muted mb-1 font-medium">Search</label>
            <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>"
                   placeholder="Product name…"
                   class="w-full px-3 py-2 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs text-muted mb-1 font-medium">Category</label>
            <select name="category_id"
                    class="w-full px-3 py-2 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit"
                class="px-5 py-2 bg-forest text-white text-sm font-semibold rounded-full hover:bg-pine transition-all">
            Filter
        </button>
        <?php if (!empty($filters['search']) || !empty($filters['category_id'])): ?>
            <a href="<?= APP_URL ?>/admin/inventory" class="px-4 py-2 text-sm text-muted hover:text-forest transition-colors">Clear</a>
        <?php endif; ?>
    </form>

    <!-- Legend -->
    <div class="flex items-center gap-4 text-xs text-muted">
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-400 inline-block"></span> In stock</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span> Low stock</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span> Out of stock</span>
    </div>

    <!-- Table -->
    <div class="bg-white border border-border rounded-2xl overflow-hidden">
        <?php if (empty($products)): ?>
            <div class="py-16 text-center">
                <p class="text-muted text-sm">No products found.</p>
            </div>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-cream/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Product</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Category</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Alert At</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Current Stock</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Update Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($products as $p):
                        $stock    = (int)$p['stock'];
                        $alert    = (int)$p['low_stock_alert'];
                        $isOut    = $stock === 0;
                        $isLow    = !$isOut && $stock <= $alert;
                        $rowClass = $isOut ? 'bg-red-50/40' : ($isLow ? 'bg-yellow-50/40' : '');
                    ?>
                        <tr class="<?= $rowClass ?> hover:bg-cream/20 transition-colors" id="row-<?= $p['id'] ?>">
                            <!-- Product -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 <?= $isOut ? 'bg-red-400' : ($isLow ? 'bg-yellow-400' : 'bg-green-400') ?>"></span>
                                    <div>
                                        <div class="font-medium text-text"><?= e($p['name']) ?></div>
                                        <div class="text-xs text-muted"><?= e($p['slug']) ?></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-4 py-3.5 text-muted text-xs"><?= e($p['category_name'] ?? '—') ?></td>

                            <!-- Alert threshold -->
                            <td class="px-4 py-3.5 text-center text-muted text-xs">&le; <?= $alert ?></td>

                            <!-- Current stock -->
                            <td class="px-4 py-3.5 text-center">
                                <span id="stock-display-<?= $p['id'] ?>"
                                      class="font-semibold text-base <?= $isOut ? 'text-red-600' : ($isLow ? 'text-yellow-600' : 'text-green-700') ?>">
                                    <?= $stock ?>
                                </span>
                                <?php if ($isOut): ?>
                                    <div class="text-[0.6rem] text-red-500 font-medium">Out of stock</div>
                                <?php elseif ($isLow): ?>
                                    <div class="text-[0.6rem] text-yellow-600 font-medium">Low stock</div>
                                <?php endif; ?>
                            </td>

                            <!-- Inline stock update -->
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <input type="number" min="0"
                                           id="stock-input-<?= $p['id'] ?>"
                                           value="<?= $stock ?>"
                                           class="w-20 px-2 py-1.5 text-sm text-center border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                                    <button onclick="updateStock(<?= $p['id'] ?>)"
                                            class="px-3 py-1.5 text-xs font-semibold bg-forest text-white rounded-lg hover:bg-pine transition-all">
                                        Save
                                    </button>
                                </div>
                                <p id="stock-msg-<?= $p['id'] ?>" class="text-[0.65rem] mt-1 hidden"></p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function updateStock(productId) {
    const input   = document.getElementById('stock-input-' + productId);
    const msg     = document.getElementById('stock-msg-' + productId);
    const display = document.getElementById('stock-display-' + productId);
    const qty     = parseInt(input.value, 10);

    if (isNaN(qty) || qty < 0) {
        showMsg(msg, 'Invalid quantity.', 'error');
        return;
    }

    try {
        const res = await fetch(`<?= APP_URL ?>/admin/inventory/${productId}/stock`, {
            method : 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token' : CSRF_TOKEN,
            },
            body: `stock=${qty}&_token=${encodeURIComponent(CSRF_TOKEN)}`,
        });
        const data = await res.json();

        if (data.success) {
            display.textContent = qty;
            showMsg(msg, 'Updated!', 'success');
        } else {
            showMsg(msg, data.message ?? 'Error.', 'error');
        }
    } catch (e) {
        showMsg(msg, 'Network error.', 'error');
    }
}

function showMsg(el, text, type) {
    el.textContent = text;
    el.className = 'text-[0.65rem] mt-1 ' + (type === 'success' ? 'text-green-600' : 'text-red-500');
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 2500);
}
</script>