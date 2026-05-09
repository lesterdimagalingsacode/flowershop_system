<?php $title = 'Products'; ?>

<div class="space-y-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-0.5">Catalog</p>
            <h2 class="text-xl font-bold text-forest" style="font-family: var(--font-display);">All Products</h2>
        </div>
        <a href="<?= APP_URL ?>/admin/products/create"
           class="bg-forest hover:bg-pine text-white text-sm font-semibold px-5 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Product
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-border rounded-2xl px-5 py-4 flex flex-wrap items-end gap-3">

        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-muted mb-1 font-medium">Search</label>
            <div class="relative">
                <input id="searchInput" type="search"
                       value="<?= e($filters['search'] ?? '') ?>"
                       placeholder="Product name…"
                       class="w-full px-3 py-2 pr-8 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                <div id="searchSpinner" class="absolute right-2.5 top-1/2 -translate-y-1/2 hidden">
                    <svg class="w-3.5 h-3.5 text-forest animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="min-w-[160px]">
            <label class="block text-xs text-muted mb-1 font-medium">Category</label>
            <select id="categoryFilter"
                    class="w-full px-3 py-2 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="min-w-[140px]">
            <label class="block text-xs text-muted mb-1 font-medium">Status</label>
            <select id="statusFilter"
                    class="w-full px-3 py-2 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                <option value="">All</option>
                <option value="1" <?= ($filters['is_active'] ?? '') === '1' ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= ($filters['is_active'] ?? '') === '0' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
    </div>

    <!-- Stats bar -->
    <p class="text-xs text-muted">
        Showing <span id="showingCount" class="font-semibold text-text"><?= count($products) ?></span>
        of <span id="totalCount" class="font-semibold text-text"><?= $total ?></span> products
    </p>

    <!-- Table -->
    <div id="productsTableWrap" class="bg-white border border-border rounded-2xl overflow-hidden transition-opacity duration-150">
        <?php if (empty($products)): ?>
            <div class="py-16 text-center">
                <div class="text-4xl mb-3">🌸</div>
                <p class="text-muted text-sm">No products found.</p>
                <a href="<?= APP_URL ?>/admin/products/create" class="text-forest text-sm font-medium hover:underline mt-1 inline-block">Add your first product</a>
            </div>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-cream/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Product</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Category</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Price</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Stock</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($products as $p): ?>
                        <?php $lowStock = (int)$p['stock'] <= (int)$p['low_stock_alert']; ?>
                        <tr class="hover:bg-cream/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <?php if ($p['image']): ?>
                                        <img src="<?= APP_URL ?>/images/products/<?= e($p['image']) ?>"
                                             alt="<?= e($p['name']) ?>"
                                             class="w-10 h-10 object-cover rounded-xl border border-border flex-shrink-0">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-xl bg-cream border border-border flex items-center justify-center flex-shrink-0 text-lg">🌸</div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="font-medium text-text"><?= e($p['name']) ?></div>
                                        <div class="text-xs text-muted"><?= e($p['slug']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-muted"><?= e($p['category_name'] ?? '—') ?></td>
                            <td class="px-4 py-3.5 text-right font-medium">₱<?= number_format((float)$p['price'], 2) ?></td>
                            <td class="px-4 py-3.5 text-right">
                                <span class="<?= $lowStock ? 'text-red-600 font-semibold' : 'text-text' ?>">
                                    <?= (int)$p['stock'] ?>
                                </span>
                                <?php if ($lowStock && (int)$p['stock'] > 0): ?>
                                    <span class="ml-1 text-[0.6rem] bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded-full font-medium">Low</span>
                                <?php elseif ((int)$p['stock'] === 0): ?>
                                    <span class="ml-1 text-[0.6rem] bg-red-100 text-red-700 px-1.5 py-0.5 rounded-full font-medium">Out</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <?php if ($p['is_active']): ?>
                                    <span class="inline-flex items-center gap-1 text-[0.7rem] font-semibold bg-green-100 text-green-700 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-[0.7rem] font-semibold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= APP_URL ?>/shop/<?= e($p['slug']) ?>" target="_blank"
                                       title="View on shop"
                                       class="p-1.5 rounded-lg text-muted hover:text-forest hover:bg-cream transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                    <a href="<?= APP_URL ?>/admin/products/<?= $p['id'] ?>/edit"
                                       title="Edit"
                                       class="p-1.5 rounded-lg text-muted hover:text-forest hover:bg-cream transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="<?= APP_URL ?>/admin/products/<?= $p['id'] ?>/delete"
                                          onsubmit="return confirm('Delete \'<?= e(addslashes($p['name'])) ?>\'? This cannot be undone.')">
                                        <?= csrf_field() ?>
                                        <button type="submit" title="Delete"
                                                class="p-1.5 rounded-lg text-muted hover:text-red-600 hover:bg-red-50 transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div id="paginationWrap" class="<?= $totalPages <= 1 ? 'hidden' : '' ?>">
        <div id="paginationInner" class="flex items-center justify-center gap-1">
            <?php if ($totalPages > 1): ?>
                <?php for ($i = 1; $i <= $totalPages; $i++):
                    $q = http_build_query(array_merge($filters, ['page' => $i]));
                ?>
                    <a href="<?= APP_URL ?>/admin/products?<?= $q ?>"
                       class="pagBtn w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-all
                              <?= $i === $page ? 'bg-forest text-white font-semibold' : 'text-muted hover:bg-cream hover:text-forest' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Hidden CSRF for JS use -->
<span id="csrfToken" data-token="<?= e(csrf_token()) ?>" class="hidden"></span>
<span id="appUrl" data-url="<?= APP_URL ?>" class="hidden"></span>

<!-- ── Live Search Script ───────────────────────────────── -->
<script>
(function () {
    const searchInput    = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter   = document.getElementById('statusFilter');
    const spinner        = document.getElementById('searchSpinner');
    const tableWrap      = document.getElementById('productsTableWrap');
    const showingCount   = document.getElementById('showingCount');
    const totalCount     = document.getElementById('totalCount');
    const paginationWrap = document.getElementById('paginationWrap');
    const paginationInner = document.getElementById('paginationInner');
    const APP_URL        = document.getElementById('appUrl').dataset.url;

    let debounceTimer = null;

    function getCsrf() {
        return document.getElementById('csrfToken')?.dataset.token ?? '';
    }

    function buildParams(page = 1) {
        const p = new URLSearchParams();
        const s = searchInput.value.trim();
        const c = categoryFilter.value;
        const a = statusFilter.value;
        if (s) p.set('search', s);
        if (c) p.set('category_id', c);
        if (a !== '') p.set('is_active', a);
        p.set('page', page);
        return p;
    }

    async function fetchProducts(page = 1) {
        spinner.classList.remove('hidden');
        tableWrap.style.opacity = '0.5';
        tableWrap.style.pointerEvents = 'none';

        try {
            const res  = await fetch(APP_URL + '/admin/products/search?' + buildParams(page));
            const data = await res.json();
            if (!data.success) throw new Error('Request failed');

            const { products, total, total_pages, page: pg } = data.data;

            showingCount.textContent = products.length.toLocaleString();
            totalCount.textContent   = total.toLocaleString();

            tableWrap.innerHTML = renderTable(products);

            if (total_pages > 1) {
                paginationInner.innerHTML = renderPagination(pg, total_pages);
                paginationWrap.classList.remove('hidden');
                attachPaginationListeners();
            } else {
                paginationWrap.classList.add('hidden');
            }

        } catch (e) {
            console.error('Search error:', e);
        } finally {
            spinner.classList.add('hidden');
            tableWrap.style.opacity = '';
            tableWrap.style.pointerEvents = '';
        }
    }

    // ── Table renderer ──────────────────────────────────
    function renderTable(products) {
        if (!products.length) {
            return `<div class="py-16 text-center">
                <div class="text-4xl mb-3">🌸</div>
                <p class="text-muted text-sm">No products found.</p>
                <a href="${APP_URL}/admin/products/create" class="text-forest text-sm font-medium hover:underline mt-1 inline-block">Add your first product</a>
            </div>`;
        }

        const rows = products.map(p => {
            const img = p.image
                ? `<img src="${APP_URL}/images/products/${esc(p.image)}" alt="${esc(p.name)}"
                        class="w-10 h-10 object-cover rounded-xl border border-border flex-shrink-0">`
                : `<div class="w-10 h-10 rounded-xl bg-cream border border-border flex items-center justify-center flex-shrink-0 text-lg">🌸</div>`;

            const stock     = parseInt(p.stock);
            const lowAlert  = parseInt(p.low_stock_alert);
            const isLow     = stock <= lowAlert;
            const stockClass = isLow ? 'text-red-600 font-semibold' : 'text-text';
            const stockBadge = stock === 0
                ? `<span class="ml-1 text-[0.6rem] bg-red-100 text-red-700 px-1.5 py-0.5 rounded-full font-medium">Out</span>`
                : isLow
                    ? `<span class="ml-1 text-[0.6rem] bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded-full font-medium">Low</span>`
                    : '';

            const statusBadge = p.is_active == 1
                ? `<span class="inline-flex items-center gap-1 text-[0.7rem] font-semibold bg-green-100 text-green-700 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active</span>`
                : `<span class="inline-flex items-center gap-1 text-[0.7rem] font-semibold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Inactive</span>`;

            return `<tr class="hover:bg-cream/30 transition-colors border-t border-border">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        ${img}
                        <div>
                            <div class="font-medium text-text">${esc(p.name)}</div>
                            <div class="text-xs text-muted">${esc(p.slug ?? '')}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3.5 text-muted">${esc(p.category_name ?? '—')}</td>
                <td class="px-4 py-3.5 text-right font-medium">₱${parseFloat(p.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-3.5 text-right">
                    <span class="${stockClass}">${stock}</span>${stockBadge}
                </td>
                <td class="px-4 py-3.5 text-center">${statusBadge}</td>
                <td class="px-5 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="${APP_URL}/shop/${esc(p.slug)}" target="_blank" title="View on shop"
                           class="p-1.5 rounded-lg text-muted hover:text-forest hover:bg-cream transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <a href="${APP_URL}/admin/products/${p.id}/edit" title="Edit"
                           class="p-1.5 rounded-lg text-muted hover:text-forest hover:bg-cream transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="${APP_URL}/admin/products/${p.id}/delete"
                              onsubmit="return confirm('Delete \\'${esc(p.name)}\\'? This cannot be undone.')">
                            <input type="hidden" name="_csrf" value="${getCsrf()}">
                            <button type="submit" title="Delete"
                                    class="p-1.5 rounded-lg text-muted hover:text-red-600 hover:bg-red-50 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>`;
        }).join('');

        return `<table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-cream/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Product</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Category</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Price</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Stock</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Status</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">${rows}</tbody>
        </table>`;
    }

    // ── Pagination renderer ──────────────────────────────
    function renderPagination(page, totalPages) {
        let html = '';
        const start = Math.max(1, page - 2);
        const end   = Math.min(totalPages, page + 2);

        if (page > 1) {
            html += `<button data-page="${page - 1}" class="pagBtn w-8 h-8 flex items-center justify-center rounded-lg text-sm text-muted hover:bg-cream hover:text-forest transition-all">‹</button>`;
        }
        for (let i = start; i <= end; i++) {
            const cls = i === page
                ? 'bg-forest text-white font-semibold'
                : 'text-muted hover:bg-cream hover:text-forest';
            html += `<button data-page="${i}" class="pagBtn w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-all ${cls}">${i}</button>`;
        }
        if (page < totalPages) {
            html += `<button data-page="${page + 1}" class="pagBtn w-8 h-8 flex items-center justify-center rounded-lg text-sm text-muted hover:bg-cream hover:text-forest transition-all">›</button>`;
        }
        return html;
    }

    function attachPaginationListeners() {
        document.querySelectorAll('.pagBtn').forEach(btn => {
            btn.addEventListener('click', () => fetchProducts(parseInt(btn.dataset.page)));
        });
    }

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ── Event listeners ──────────────────────────────────
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchProducts(1), 350);
    });
    categoryFilter.addEventListener('change', () => fetchProducts(1));
    statusFilter.addEventListener('change',   () => fetchProducts(1));

    attachPaginationListeners();
})();
</script>