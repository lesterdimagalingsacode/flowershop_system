<?php
// app/views/shop/show.php
$title = $product['name'];
$imageUrl = $product['image']
    ? APP_URL . '/images/products/' . e($product['image'])
    : 'https://picsum.photos/seed/' . $product['id'] . '/800/600';
?>

<div class="bg-cream min-h-screen">
    <div class="max-w-5xl mx-auto px-6 py-10">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-muted mb-8">
            <a href="<?= APP_URL ?>/shop" class="hover:text-forest transition">Shop</a>
            <span>/</span>
            <a href="<?= APP_URL ?>/shop?category_id=<?= $product['category_id'] ?>"
               class="hover:text-forest transition">
                <?= e($product['category_name'] ?? 'Products') ?>
            </a>
            <span>/</span>
            <span class="text-text"><?= e($product['name']) ?></span>
        </nav>

        <div class="bg-white border border-border rounded-3xl overflow-hidden shadow-sm">
            <div class="flex flex-col md:flex-row">

                <!-- Image -->
                <div class="w-full md:w-2/5 flex-shrink-0 bg-ivory">
                    <img src="<?= $imageUrl ?>"
                         alt="<?= e($product['name']) ?>"
                         class="w-full h-72 md:h-full object-cover"
                         onerror="this.src='https://picsum.photos/seed/<?= $product['id'] ?>/800/600'">
                </div>

                <!-- Details -->
                <div class="flex-1 p-8 flex flex-col">

                    <!-- Category -->
                    <p class="text-[0.65rem] text-gold uppercase tracking-widest mb-2">
                        <?= e($product['category_name'] ?? '') ?>
                    </p>

                    <!-- Name -->
                    <h1 class="text-3xl text-text mb-3" style="font-family: var(--font-display);">
                        <?= e($product['name']) ?>
                    </h1>

                    <!-- Price -->
                    <p class="text-forest font-bold text-2xl mb-5">
                        ₱<?= number_format((float)$product['price'], 2) ?>
                    </p>

                    <!-- Description -->
                    <p class="text-sm text-muted leading-relaxed mb-8 flex-1">
                        <?= e($product['description'] ?? 'No description available.') ?>
                    </p>

                    <!-- Stock badge -->
                    <div class="mb-6">
                        <?php if ($product['stock'] <= 0): ?>
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-100 px-3 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
                                Out of Stock
                            </span>
                        <?php elseif ($product['stock'] <= $product['low_stock_alert']): ?>
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600 bg-amber-50 border border-amber-100 px-3 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                                Low Stock — only <?= $product['stock'] ?> left
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-600 bg-green-50 border border-green-100 px-3 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                In Stock
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Add to cart -->
                    <?php if ($product['stock'] > 0): ?>
                        <?php if (Session::isLoggedIn()): ?>
                            <div class="flex items-center gap-3">
                                <!-- Quantity control -->
                                <div class="flex items-center border border-border rounded-full overflow-hidden">
                                    <button id="qtyDec"
                                            class="w-10 h-10 flex items-center justify-center text-muted hover:text-forest hover:bg-cream transition text-lg"
                                            disabled>−</button>
                                    <span id="qtyDisplay"
                                          class="w-10 text-center text-sm font-medium text-text">1</span>
                                    <button id="qtyInc"
                                            class="w-10 h-10 flex items-center justify-center text-muted hover:text-forest hover:bg-cream transition text-lg"
                                            <?= $product['stock'] <= 1 ? 'disabled' : '' ?>>+</button>
                                </div>
                                <!-- Add to cart button -->
                                <button id="atcBtn"
                                        class="flex-1 bg-forest hover:bg-pine text-white font-medium text-sm py-3 rounded-full transition-all hover:-translate-y-px shadow-sm">
                                    Add to Cart
                                </button>
                            </div>
                            <p id="atcMsg" class="hidden text-xs text-center mt-3"></p>
                        <?php else: ?>
                            <a href="<?= APP_URL ?>/login"
                               class="block w-full bg-forest hover:bg-pine text-white font-medium text-sm text-center py-3 rounded-full transition-all hover:-translate-y-px shadow-sm">
                                Login to Add to Cart
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-sm text-muted italic text-center py-3 bg-cream rounded-full border border-border">
                            Currently unavailable
                        </p>
                    <?php endif; ?>

                    <!-- Back link -->
                    <div class="mt-6 pt-6 border-t border-border">
                        <a href="<?= APP_URL ?>/shop"
                           class="inline-flex items-center gap-2 text-xs text-muted hover:text-forest transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Shop
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php if (Session::isLoggedIn() && $product['stock'] > 0): ?>
<script>
(function () {
    const CART_ADD_URL = '<?= APP_URL ?>/shop/cart/add';
    const CSRF_TOKEN   = '<?= csrf_token() ?>';
    const MAX_STOCK    = <?= (int)$product['stock'] ?>;
    const PRODUCT_ID   = <?= (int)$product['id'] ?>;

    let qty = 1;

    const qtyDec     = document.getElementById('qtyDec');
    const qtyInc     = document.getElementById('qtyInc');
    const qtyDisplay = document.getElementById('qtyDisplay');
    const atcBtn     = document.getElementById('atcBtn');
    const atcMsg     = document.getElementById('atcMsg');

    qtyDec.addEventListener('click', () => {
        if (qty > 1) {
            qty--;
            qtyDisplay.textContent = qty;
            qtyDec.disabled = qty <= 1;
            qtyInc.disabled = qty >= MAX_STOCK;
        }
    });

    qtyInc.addEventListener('click', () => {
        if (qty < MAX_STOCK) {
            qty++;
            qtyDisplay.textContent = qty;
            qtyDec.disabled = qty <= 1;
            qtyInc.disabled = qty >= MAX_STOCK;
        }
    });

    atcBtn.addEventListener('click', async function () {
        const original  = atcBtn.textContent;
        atcBtn.disabled = true;
        atcBtn.textContent = 'Adding...';

        try {
            const res  = await fetch(CART_ADD_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({
                    _token:     CSRF_TOKEN,
                    product_id: PRODUCT_ID,
                    quantity:   qty,
                }),
            });

            const data = await res.json();

            if (data.success) {
                atcBtn.textContent = '✓ Added!';
                document.querySelectorAll('[data-cart-count]').forEach(el => {
                    el.textContent = data.data.cart_count;
                    el.classList.remove('hidden');
                });
                if (typeof Toast !== 'undefined') Toast.success(data.message);
                setTimeout(() => {
                    atcBtn.textContent = original;
                    atcBtn.disabled    = false;
                }, 1500);
            } else {
                atcBtn.textContent = original;
                atcBtn.disabled    = false;
                if (typeof Toast !== 'undefined') Toast.error(data.message || 'Could not add to cart.');
            }
        } catch (e) {
            atcBtn.textContent = original;
            atcBtn.disabled    = false;
            if (typeof Toast !== 'undefined') Toast.error('Something went wrong.');
        }
    });
})();
</script>
<?php endif; ?>