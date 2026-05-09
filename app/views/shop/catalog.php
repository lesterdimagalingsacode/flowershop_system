    <?php $title = 'Shop'; ?>

    <div class="bg-cream min-h-screen">

        <!-- ── Search Bar ── -->
        <div class="bg-white border-b border-border sticky top-16 z-40">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex gap-3 items-center">
                    <div class="relative flex-1 max-w-xl">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                        <input
                            type="text"
                            id="searchInput"
                            value="<?= e($filters['search']) ?>"
                            placeholder="Search flowers, bouquets..."
                            class="w-full bg-cream border border-border rounded-full pl-11 pr-4 py-2.5 text-sm text-text placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                            autocomplete="off"
                        >
                        <div id="searchSpinner" class="absolute right-4 top-1/2 -translate-y-1/2 hidden">
                            <svg class="w-4 h-4 text-forest animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </div>
                    </div>
                    <button id="clearSearch" class="hidden text-sm text-muted hover:text-forest transition px-3 py-2">
                        Clear
                    </button>
                </div>
            </div>

            <!-- ── Category Tabs ── -->
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex gap-1 overflow-x-auto scrollbar-hide">
                    <button
                        data-category=""
                        class="category-tab flex-shrink-0 text-xs font-medium px-4 py-2 rounded-t-xl border-b-2 border-forest text-forest bg-forest/5 transition-all"
                    >All</button>
                    <?php foreach ($categories as $cat): ?>
                    <button
                        data-category="<?= $cat['id'] ?>"
                        data-target="section-<?= $cat['id'] ?>"
                        class="category-tab flex-shrink-0 text-xs font-medium px-4 py-2 rounded-t-xl border-b-2 border-transparent text-muted hover:text-forest transition-all"
                    ><?= e($cat['name']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex gap-8">

                <!-- ── Sidebar ── -->
                <aside class="hidden lg:block w-56 flex-shrink-0 self-start sticky top-52">

                    <!-- Price Range -->
                    <div class="bg-white border border-border rounded-2xl p-5 mb-4">
                        <h3 class="text-xs font-medium text-text tracking-widest uppercase mb-4">Price Range</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[0.65rem] text-muted uppercase tracking-wider">Min (₱)</label>
                                <input
                                    type="number"
                                    id="minPrice"
                                    value="<?= e($filters['min_price']) ?>"
                                    placeholder="0"
                                    min="0"
                                    class="w-full mt-1 bg-cream border border-border rounded-xl px-3 py-2 text-sm text-text focus:outline-none focus:border-forest focus:ring-1 focus:ring-forest/20 transition"
                                >
                            </div>
                            <div>
                                <label class="text-[0.65rem] text-muted uppercase tracking-wider">Max (₱)</label>
                                <input
                                    type="number"
                                    id="maxPrice"
                                    value="<?= e($filters['max_price']) ?>"
                                    placeholder="<?= number_format((float)$priceRange['max_price']) ?>"
                                    min="0"
                                    class="w-full mt-1 bg-cream border border-border rounded-xl px-3 py-2 text-sm text-text focus:outline-none focus:border-forest focus:ring-1 focus:ring-forest/20 transition"
                                >
                            </div>
                            <button id="applyPrice"
                                class="w-full bg-forest hover:bg-pine text-white text-xs font-medium py-2 rounded-full transition">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="bg-white border border-border rounded-2xl p-5">
                        <h3 class="text-xs font-medium text-text tracking-widest uppercase mb-4">Availability</h3>
                        <div class="space-y-2.5">
                            <?php
                            $availOptions = [
                                ''             => 'All',
                                'in_stock'     => 'In Stock',
                                'out_of_stock' => 'Out of Stock',
                            ];
                            foreach ($availOptions as $val => $label):
                            ?>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="radio" name="availability" value="<?= $val ?>"
                                    <?= ($filters['availability'] ?? '') === $val ? 'checked' : '' ?>
                                    class="accent-forest availability-filter">
                                <span class="text-sm text-muted group-hover:text-forest transition"><?= $label ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </aside>

                <!-- ── Main Content ── -->
                <div class="flex-1 min-w-0">

                    <!-- Search results -->
                    <div id="searchResults" class="hidden">
                        <div class="flex items-center justify-between mb-6">
                            <p class="text-sm text-muted">
                                Showing <span id="searchCount" class="text-text font-medium">0</span> results
                                for <span id="searchTerm" class="text-forest font-medium"></span>
                            </p>
                        </div>
                        <div id="searchGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <!-- Filled by JS -->
                        </div>
                        <div id="searchEmpty" class="hidden text-center py-24">
                            <div class="text-6xl mb-4">🌿</div>
                            <h3 class="text-xl text-text mb-2" style="font-family: var(--font-display);">No flowers found</h3>
                            <p class="text-muted text-sm">Try a different search term.</p>
                        </div>
                    </div>

                    <!-- Category sections -->
                    <div id="categorySections">
                        <?php if (empty($sections)): ?>
                        <div class="text-center py-24">
                            <div class="text-6xl mb-4">🌿</div>
                            <h3 class="text-xl text-text mb-2" style="font-family: var(--font-display);">No products yet</h3>
                            <p class="text-muted text-sm">Check back soon for fresh arrangements.</p>
                        </div>
                        <?php else: ?>
                            <?php foreach ($sections as $section): ?>
                            <?php if (empty($section['products'])) continue; ?>

                            <div id="section-<?= $section['category']['id'] ?>" class="mb-12 category-section">

                                <div class="flex items-end justify-between mb-5">
                                    <div>
                                        <p class="text-[0.65rem] text-gold uppercase tracking-widest mb-1">
                                            <?= $section['total'] ?> arrangements
                                        </p>
                                        <h2 class="text-2xl text-text" style="font-family: var(--font-display);">
                                            <?= e($section['category']['name']) ?>
                                        </h2>
                                    </div>
                                    <?php if ($section['total'] > 8): ?>
                                    <a href="<?= APP_URL ?>/shop?category_id=<?= $section['category']['id'] ?>"
                                    class="text-xs text-forest hover:text-pine font-medium transition">
                                        View all &rarr;
                                    </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Horizontal scroll -->
                                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                                    <?php foreach ($section['products'] as $product): ?>
                                    <div class="flex-shrink-0 w-56 bg-white border border-border rounded-2xl overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group">

                                        <!-- Clickable image → opens modal -->
                                        <div class="block relative overflow-hidden cursor-pointer product-modal-trigger"
                                            data-product-id="<?= $product['id'] ?>">
                                            <?php
                                            $imageUrl = $product['image']
                                                ? APP_URL . '/images/products/' . e($product['image'])
                                                : 'https://picsum.photos/seed/' . $product['id'] . '/400/300';
                                            ?>
                                            <img
                                                src="<?= $imageUrl ?>"
                                                alt="<?= e($product['name']) ?>"
                                                class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300"
                                                loading="lazy"
                                            >
                                            <?php if ($product['stock'] <= 0): ?>
                                                <div class="absolute top-2 left-2 bg-bark text-white text-[0.6rem] font-medium px-2 py-0.5 rounded-full">Out of Stock</div>
                                            <?php elseif ($product['stock'] <= $product['low_stock_alert']): ?>
                                                <div class="absolute top-2 left-2 bg-gold text-white text-[0.6rem] font-medium px-2 py-0.5 rounded-full">Low Stock</div>
                                            <?php endif; ?>
                                            <!-- View detail hint -->
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-200 flex items-center justify-center">
                                                <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 text-forest text-[0.65rem] font-medium px-3 py-1 rounded-full">
                                                    View Details
                                                </span>
                                            </div>
                                        </div>

                                        <div class="p-3.5">
                                            <!-- Name → opens modal -->
                                            <h3 class="font-semibold text-text text-sm mb-1 hover:text-forest transition line-clamp-1 cursor-pointer product-modal-trigger"
                                                style="font-family: var(--font-display);"
                                                data-product-id="<?= $product['id'] ?>">
                                                <?= e($product['name']) ?>
                                            </h3>
                                            <p class="text-[0.7rem] text-muted mb-3 line-clamp-2 leading-relaxed">
                                                <?= e($product['description'] ?? '') ?>
                                            </p>
                                            <div class="flex items-center justify-between">
                                                <span class="text-forest font-bold text-sm">
                                                    ₱<?= number_format((float)$product['price'], 2) ?>
                                                </span>
                                                <?php if ($product['stock'] > 0): ?>
                                                    <?php if (Session::isLoggedIn()): ?>
                                                    <button type="button"
                                                        class="atc-btn bg-forest hover:bg-pine text-white text-[0.65rem] font-medium px-3 py-1.5 rounded-full transition-all"
                                                        data-product-id="<?= $product['id'] ?>">
                                                        Add
                                                    </button>
                                                    <?php else: ?>
                                                    <a href="<?= APP_URL ?>/login"
                                                    class="bg-forest hover:bg-pine text-white text-[0.65rem] font-medium px-3 py-1.5 rounded-full transition-colors">
                                                        Add
                                                    </a>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-[0.65rem] text-muted italic">Unavailable</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════
        PRODUCT DETAIL MODAL
        ══════════════════════════════════════════════════════ -->
    <div id="productModal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 hidden"
        aria-modal="true" role="dialog">

        <!-- Backdrop -->
        <div id="modalBackdrop"
            class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>

        <!-- Panel -->
        <div id="modalPanel"
            class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden
                    opacity-0 translate-y-4 transition-all duration-300 flex flex-col">

            <!-- Close button -->
            <button id="modalClose"
                    class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-cream hover:bg-border text-muted hover:text-text transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Loading state -->
            <div id="modalLoading" class="flex items-center justify-center py-24">
                <svg class="w-8 h-8 text-forest animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </div>

            <!-- Content (filled by JS) -->
            <div id="modalContent" style="display:none;" class="flex flex-col md:flex-row overflow-hidden flex-1">

                <!-- Image -->
                <div class="w-full md:w-2/5 flex-shrink-0 bg-ivory">
                    <img id="modalImage" src="" alt=""
                        class="w-full h-56 md:h-full object-cover">
                </div>

                <!-- Details -->
                <div class="flex-1 p-6 overflow-y-auto flex flex-col">

                    <!-- Category -->
                    <p id="modalCategory"
                    class="text-[0.65rem] text-gold uppercase tracking-widest mb-2"></p>

                    <!-- Name -->
                    <h2 id="modalName"
                        class="text-2xl text-text mb-1" style="font-family: var(--font-display);"></h2>

                    <!-- Price -->
                    <p id="modalPrice"
                    class="text-forest font-bold text-xl mb-4"></p>

                    <!-- Description -->
                    <p id="modalDescription"
                    class="text-sm text-muted leading-relaxed mb-6 flex-1"></p>

                    <!-- Add to cart section -->
                    <div id="modalCartSection">
                        <!-- Quantity + Add (for logged in) -->
                        <div id="modalAddSection" class="hidden">
                            <div class="flex items-center gap-3">
                                <!-- Quantity control -->
                                <div class="flex items-center border border-border rounded-full overflow-hidden">
                                    <button id="modalQtyDec"
                                            class="w-9 h-9 flex items-center justify-center text-muted hover:text-forest hover:bg-cream transition text-lg">−</button>
                                    <span id="modalQtyDisplay"
                                        class="w-8 text-center text-sm font-medium text-text">1</span>
                                    <button id="modalQtyInc"
                                            class="w-9 h-9 flex items-center justify-center text-muted hover:text-forest hover:bg-cream transition text-lg">+</button>
                                </div>
                                <!-- Add to cart -->
                                <button id="modalAtcBtn"
                                        class="flex-1 bg-forest hover:bg-pine text-white font-medium text-sm py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                                    Add to Cart
                                </button>
                            </div>
                        </div>

                        <!-- Out of stock -->
                        <div id="modalOutOfStock" class="hidden">
                            <p class="text-sm text-muted italic text-center py-2">This arrangement is currently unavailable.</p>
                        </div>

                        <!-- Login prompt -->
                        <div id="modalLoginPrompt" class="hidden">
                            <a id="modalLoginLink" href="<?= APP_URL ?>/login"
                            class="block w-full bg-forest hover:bg-pine text-white font-medium text-sm text-center py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                                Login to Add to Cart
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>


    <script>
    const SEARCH_URL   = '<?= APP_URL ?>/shop/search';
    const CART_ADD_URL = '<?= APP_URL ?>/shop/cart/add';
    const APP_URL      = '<?= APP_URL ?>';
    const IS_LOGGED_IN = <?= Session::isLoggedIn() ? 'true' : 'false' ?>;
    const CSRF_TOKEN   = '<?= csrf_token() ?>';

    let searchTimeout  = null;
    let activeCategory = '';
    let minPrice       = '';
    let maxPrice       = '';
    let availability   = '';

    const searchInput      = document.getElementById('searchInput');
    const searchResults    = document.getElementById('searchResults');
    const categorySections = document.getElementById('categorySections');
    const searchGrid       = document.getElementById('searchGrid');
    const searchEmpty      = document.getElementById('searchEmpty');
    const searchCount      = document.getElementById('searchCount');
    const searchTermEl     = document.getElementById('searchTerm');
    const searchSpinner    = document.getElementById('searchSpinner');
    const clearBtn         = document.getElementById('clearSearch');
    const categoryTabs     = document.querySelectorAll('.category-tab');

    // ══════════════════════════════════════════════════════
    //  PRODUCT MODAL
    // ══════════════════════════════════════════════════════

    const modal        = document.getElementById('productModal');
    const backdrop     = document.getElementById('modalBackdrop');
    const panel        = document.getElementById('modalPanel');
    const modalClose   = document.getElementById('modalClose');
    const modalLoading = document.getElementById('modalLoading');
    const modalContent = document.getElementById('modalContent');

    let modalQty       = 1;
    let modalMaxStock  = 0;
    let modalProductId = null;

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            backdrop.style.opacity = '1';
            panel.style.opacity    = '1';
            panel.style.transform  = 'translateY(0)';
        });
    }

    function closeModal() {
        backdrop.style.opacity = '0';
        panel.style.opacity    = '0';
        panel.style.transform  = 'translateY(1rem)';
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            // Reset state
            modalLoading.classList.remove('hidden');
            modalContent.style.display = 'none';
            modalQty = 1;
            modalProductId = null;
        }, 300);
    }

    modalClose.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    async function loadProductModal(productId) {
        modalProductId = productId;

        // Reset & open
        modalLoading.classList.remove('hidden');
        modalContent.style.display = 'none';
        openModal();

        try {
            const res  = await fetch(`${APP_URL}/shop/product/${productId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();

            if (!json.success) { closeModal(); return; }

            const p = json.data;

            // Populate modal
            document.getElementById('modalImage').src       = p.image_url;
            document.getElementById('modalImage').alt       = p.name;
            document.getElementById('modalCategory').textContent    = p.category_name ?? '';
            document.getElementById('modalName').textContent        = p.name;
            document.getElementById('modalPrice').textContent       = '₱' + parseFloat(p.price).toLocaleString('en-PH', { minimumFractionDigits: 2 });
            document.getElementById('modalDescription').textContent = p.description ?? 'No description available.';

            // Cart section
            const addSection    = document.getElementById('modalAddSection');
            const outOfStock    = document.getElementById('modalOutOfStock');
            const loginPrompt   = document.getElementById('modalLoginPrompt');

            addSection.classList.add('hidden');
            outOfStock.classList.add('hidden');
            loginPrompt.classList.add('hidden');

            if (p.stock <= 0) {
                outOfStock.classList.remove('hidden');
            } else if (!IS_LOGGED_IN) {
                loginPrompt.classList.remove('hidden');
            } else {
                modalMaxStock = p.stock;
                modalQty      = 1;
                document.getElementById('modalQtyDisplay').textContent = '1';
                document.getElementById('modalQtyInc').disabled = p.stock <= 1;
                document.getElementById('modalQtyDec').disabled = true;
                addSection.classList.remove('hidden');
            }

            // Show content
            modalLoading.classList.add('hidden');
            modalContent.style.display = 'flex';

        } catch (e) {
            closeModal();
            Toast.error('Could not load product details.');
        }
    }

    // Quantity controls inside modal
    document.getElementById('modalQtyDec').addEventListener('click', () => {
        if (modalQty > 1) {
            modalQty--;
            document.getElementById('modalQtyDisplay').textContent = modalQty;
            document.getElementById('modalQtyDec').disabled = modalQty <= 1;
            document.getElementById('modalQtyInc').disabled = modalQty >= modalMaxStock;
        }
    });

    document.getElementById('modalQtyInc').addEventListener('click', () => {
        if (modalQty < modalMaxStock) {
            modalQty++;
            document.getElementById('modalQtyDisplay').textContent = modalQty;
            document.getElementById('modalQtyDec').disabled = modalQty <= 1;
            document.getElementById('modalQtyInc').disabled = modalQty >= modalMaxStock;
        }
    });

    // Add to cart from modal
    document.getElementById('modalAtcBtn').addEventListener('click', async function () {
        const btn      = this;
        const original = btn.textContent;
        btn.disabled    = true;
        btn.textContent = 'Adding...';

        try {
            const res  = await fetch(CART_ADD_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({
                    _token:     CSRF_TOKEN,
                    product_id: modalProductId,
                    quantity:   modalQty,
                }),
            });

            const data = await res.json();

            if (data.success) {
                btn.textContent = '✓ Added!';
                document.querySelectorAll('[data-cart-count]').forEach(el => {
                    el.textContent = data.data.cart_count;
                    el.classList.remove('hidden');
                });
                Toast.success(data.message);
                setTimeout(() => {
                    btn.textContent = original;
                    btn.disabled    = false;
                }, 1500);
            } else {
                btn.textContent = original;
                btn.disabled    = false;
                Toast.error(data.message || 'Could not add to cart.');
            }
        } catch (e) {
            btn.textContent = original;
            btn.disabled    = false;
            Toast.error('Something went wrong.');
        }
    });

    // Attach modal triggers to static PHP cards
    function attachModalTriggers(container) {
        container.querySelectorAll('.product-modal-trigger').forEach(el => {
            el.addEventListener('click', () => loadProductModal(el.dataset.productId));
        });
    }
    attachModalTriggers(document);

    // ══════════════════════════════════════════════════════
    //  ADD TO CART (card button)
    // ══════════════════════════════════════════════════════

    async function addToCart(btn, productId) {
        const original = btn.textContent;
        btn.disabled    = true;
        btn.textContent = '...';

        try {
            const res  = await fetch(CART_ADD_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({
                    _token:     CSRF_TOKEN,
                    product_id: productId,
                    quantity:   1,
                }),
            });

            const data = await res.json();

            if (data.success) {
                btn.textContent = '✓';
                document.querySelectorAll('[data-cart-count]').forEach(el => {
                    el.textContent = data.data.cart_count;
                    el.classList.remove('hidden');
                });
                Toast.success(data.message);
                setTimeout(() => {
                    btn.textContent = original;
                    btn.disabled    = false;
                }, 1500);
            } else {
                btn.textContent = original;
                btn.disabled    = false;
                Toast.error(data.message || 'Could not add to cart.');
            }
        } catch (e) {
            btn.textContent = original;
            btn.disabled    = false;
            Toast.error('Something went wrong.');
        }
    }

    document.querySelectorAll('.atc-btn').forEach(btn => {
        btn.addEventListener('click', () => addToCart(btn, btn.dataset.productId));
    });

    // ══════════════════════════════════════════════════════
    //  SEARCH
    // ══════════════════════════════════════════════════════

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        const query = searchInput.value.trim();
        clearBtn.classList.toggle('hidden', !query);

        if (!query && !minPrice && !maxPrice && !availability) {
            showSections();
            return;
        }

        searchSpinner.classList.remove('hidden');
        searchTimeout = setTimeout(fetchProducts, 400);
    });

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        clearBtn.classList.add('hidden');
        showSections();
    });

    document.getElementById('applyPrice').addEventListener('click', () => {
        minPrice = document.getElementById('minPrice').value;
        maxPrice = document.getElementById('maxPrice').value;
        fetchProducts();
    });

    document.querySelectorAll('.availability-filter').forEach(radio => {
        radio.addEventListener('change', () => {
            availability = radio.value;
            fetchProducts();
        });
    });

    categoryTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            categoryTabs.forEach(t => {
                t.classList.remove('border-forest', 'text-forest', 'bg-forest/5');
                t.classList.add('border-transparent', 'text-muted');
            });
            tab.classList.add('border-forest', 'text-forest', 'bg-forest/5');
            tab.classList.remove('border-transparent', 'text-muted');

            activeCategory = tab.dataset.category;
            const target   = tab.dataset.target;

            if (!activeCategory) {
                document.querySelectorAll('.category-section').forEach(s => s.classList.remove('hidden'));
                if (!searchInput.value.trim()) showSections();
            } else if (target) {
                const section = document.getElementById(target);
                if (section) {
                    const top = section.getBoundingClientRect().top + window.scrollY - 200;
                    window.scrollTo({ top, behavior: 'smooth' });
                    document.querySelectorAll('.category-section').forEach(s => s.classList.remove('hidden'));
                    if (!searchInput.value.trim()) showSections();
                }
            }
        });
    });

    async function fetchProducts() {
        const query  = searchInput.value.trim();
        const params = new URLSearchParams();

        if (query)          params.set('search',       query);
        if (activeCategory) params.set('category_id',  activeCategory);
        if (minPrice)       params.set('min_price',     minPrice);
        if (maxPrice)       params.set('max_price',     maxPrice);
        if (availability)   params.set('availability',  availability);

        try {
            searchSpinner.classList.remove('hidden');
            const res  = await fetch(`${SEARCH_URL}?${params}`, {
                headers: { 'Accept': 'application/json' }
            });
            const json = await res.json();
            if (json.success) renderSearchResults(json.data, query);
        } catch (err) {
            console.error('Search error:', err);
        } finally {
            searchSpinner.classList.add('hidden');
        }
    }

    function renderSearchResults(data, query) {
        categorySections.classList.add('hidden');
        searchResults.classList.remove('hidden');
        searchCount.textContent  = data.total;
        searchTermEl.textContent = query ? `"${query}"` : 'applied filters';

        if (data.products.length === 0) {
            searchGrid.classList.add('hidden');
            searchEmpty.classList.remove('hidden');
            return;
        }

        searchGrid.classList.remove('hidden');
        searchEmpty.classList.add('hidden');
        searchGrid.innerHTML = data.products.map(productCard).join('');

        // Attach handlers to newly rendered cards
        searchGrid.querySelectorAll('.atc-btn').forEach(btn => {
            btn.addEventListener('click', () => addToCart(btn, btn.dataset.productId));
        });
        attachModalTriggers(searchGrid);
    }

    function productCard(p) {
        const imageUrl = p.image_url ?? `https://picsum.photos/seed/${p.id}/400/300`;

        const badge = p.stock <= 0
            ? `<div class="absolute top-2 left-2 bg-bark text-white text-[0.6rem] font-medium px-2 py-0.5 rounded-full">Out of Stock</div>`
            : p.stock <= p.low_stock
            ? `<div class="absolute top-2 left-2 bg-gold text-white text-[0.6rem] font-medium px-2 py-0.5 rounded-full">Low Stock</div>`
            : '';

        const addBtn = p.stock > 0
            ? IS_LOGGED_IN
                ? `<button type="button"
                    class="atc-btn bg-forest hover:bg-pine text-white text-xs font-medium px-4 py-1.5 rounded-full transition-all"
                    data-product-id="${p.id}">Add</button>`
                : `<a href="${APP_URL}/login"
                    class="bg-forest hover:bg-pine text-white text-xs font-medium px-4 py-1.5 rounded-full transition-colors">Add</a>`
            : `<span class="text-xs text-muted italic">Unavailable</span>`;

        return `
        <div class="bg-white border border-border rounded-2xl overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group">
            <div class="block relative overflow-hidden cursor-pointer product-modal-trigger" data-product-id="${p.id}">
                <img src="${imageUrl}" alt="${p.name}"
                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                ${badge}
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-200 flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 text-forest text-[0.65rem] font-medium px-3 py-1 rounded-full">View Details</span>
                </div>
            </div>
            <div class="p-4">
                <p class="text-[0.65rem] text-gold uppercase tracking-widest mb-1">${p.category_name}</p>
                <h3 class="font-semibold text-text mb-1 hover:text-forest transition line-clamp-1 cursor-pointer product-modal-trigger"
                    style="font-family: var(--font-display);" data-product-id="${p.id}">${p.name}</h3>
                <p class="text-xs text-muted mb-4 leading-relaxed line-clamp-2">${p.description}</p>
                <div class="flex items-center justify-between">
                    <span class="text-forest font-bold text-sm">₱${p.price}</span>
                    ${addBtn}
                </div>
            </div>
        </div>`;
    }

    function showSections() {
        searchResults.classList.add('hidden');
        categorySections.classList.remove('hidden');
        searchSpinner.classList.add('hidden');
    }
    </script>