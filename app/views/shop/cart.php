<?php $title = 'My Cart'; ?>

<div class="bg-cream min-h-screen py-10" id="cart-page">
    <div class="max-w-5xl mx-auto px-4 md:px-6">

        <!-- Header -->
        <div class="mb-8">
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-1">Shopping</p>
            <h1 class="text-4xl text-text" style="font-family: var(--font-display);">My Cart</h1>
        </div>

        <?php if (empty($cart)): ?>
        <!-- Empty cart -->
        <div class="text-center py-24" id="empty-cart">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-2xl text-text mb-2" style="font-family: var(--font-display);">Your cart is empty</h3>
            <p class="text-muted text-sm mb-8">Add some beautiful arrangements to get started.</p>
            <a href="<?= APP_URL ?>/shop"
               class="bg-forest hover:bg-pine text-white font-medium px-8 py-3 rounded-full text-sm transition-all hover:-translate-y-px shadow-sm">
                Browse the Shop
            </a>
        </div>

        <?php else: ?>
        <div class="flex gap-8 flex-col lg:flex-row" id="cart-content">

            <!-- Cart items -->
            <div class="flex-1">
                <div class="bg-white border border-border rounded-2xl overflow-hidden" id="cart-items">

                    <?php foreach ($cart as $item): ?>
                    <div class="flex items-center gap-4 p-5 border-b border-border last:border-b-0 cart-row"
                         data-product-id="<?= $item['product_id'] ?>">

                        <!-- Image -->
                        <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-ivory">
                            <?php
                            $imageUrl = $item['image']
                                ? APP_URL . '/images/products/' . e($item['image'])
                                : 'https://picsum.photos/seed/' . $item['product_id'] . '/200/200';
                            ?>
                            <img src="<?= $imageUrl ?>" alt="<?= e($item['name']) ?>"
                                 class="w-full h-full object-cover">
                        </div>

                        <!-- Details -->
                        <div class="flex-1 min-w-0">
                            <a href="<?= APP_URL ?>/shop/<?= e($item['slug']) ?>">
                                <h3 class="font-semibold text-text hover:text-forest transition line-clamp-1"
                                    style="font-family: var(--font-display);">
                                    <?= e($item['name']) ?>
                                </h3>
                            </a>
                            <p class="text-sm text-forest font-bold mt-0.5">
                                ₱<?= number_format($item['price'], 2) ?>
                            </p>
                        </div>

                        <!-- Quantity controls -->
                        <div class="flex items-center border border-border rounded-full overflow-hidden">
                            <button type="button"
                                class="qty-btn w-8 h-8 flex items-center justify-center text-muted hover:text-forest hover:bg-cream transition text-lg"
                                data-product-id="<?= $item['product_id'] ?>"
                                data-action="decrease">
                                −
                            </button>
                            <span class="qty-display w-8 text-center text-sm font-medium text-text">
                                <?= $item['quantity'] ?>
                            </span>
                            <button type="button"
                                class="qty-btn w-8 h-8 flex items-center justify-center text-muted hover:text-forest hover:bg-cream transition text-lg disabled:opacity-30"
                                data-product-id="<?= $item['product_id'] ?>"
                                data-action="increase"
                                data-max="<?= $item['stock'] ?>"
                                <?= $item['quantity'] >= $item['stock'] ? 'disabled' : '' ?>>
                                +
                            </button>
                        </div>

                        <!-- Item subtotal -->
                        <div class="text-right w-24 flex-shrink-0">
                            <p class="text-sm font-bold text-text item-total">
                                ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
                            </p>
                        </div>

                        <!-- Remove -->
                        <button type="button"
                            class="remove-btn text-muted hover:text-red-500 transition p-1"
                            data-product-id="<?= $item['product_id'] ?>"
                            title="Remove item">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                    </div>
                    <?php endforeach; ?>

                </div>

                <!-- Cart actions -->
                <div class="flex justify-between items-center mt-4">
                    <a href="<?= APP_URL ?>/shop"
                       class="text-sm text-muted hover:text-forest transition flex items-center gap-1">
                        ← Continue Shopping
                    </a>
                    <form method="POST" action="<?= APP_URL ?>/shop/cart/clear">
                        <?= csrf_field() ?>
                        <button type="submit"
                            class="text-sm text-muted hover:text-red-500 transition"
                            onclick="return confirm('Clear your entire cart?')">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order summary -->
            <div class="w-full lg:w-80 flex-shrink-0">
                <div class="bg-white border border-border rounded-2xl p-6 sticky top-24">
                    <h2 class="text-xl text-text mb-5" style="font-family: var(--font-display);">Order Summary</h2>

                    <div class="space-y-3 mb-5">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Subtotal</span>
                            <span class="text-text font-medium" id="summary-subtotal">
                                ₱<?= number_format($subtotal, 2) ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Delivery</span>
                            <span class="text-text font-medium">
                                <?= $delivery > 0 ? '₱' . number_format($delivery, 2) : 'Free' ?>
                            </span>
                        </div>
                        <div class="h-px bg-border"></div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-text">Total</span>
                            <span class="font-bold text-forest text-lg" id="summary-total">
                                ₱<?= number_format($total, 2) ?>
                            </span>
                        </div>
                    </div>

                    <a href="<?= APP_URL ?>/shop/checkout"
                       class="block w-full bg-forest hover:bg-pine text-white font-medium text-sm text-center px-6 py-3.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                        Proceed to Checkout
                    </a>

                    <!-- Trust badges -->
                    <div class="mt-5 pt-5 border-t border-border space-y-2">
                        <div class="flex items-center gap-2 text-xs text-muted">
                            <span>🔒</span> Secure checkout
                        </div>
                        <div class="flex items-center gap-2 text-xs text-muted">
                            <span>🌿</span> Fresh daily arrangements
                        </div>
                        <div class="flex items-center gap-2 text-xs text-muted">
                            <span>💛</span> Made with love
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    const CSRF = '<?= csrf_token() ?>';
    const BASE = '<?= APP_URL ?>';

    // ── Helpers ──────────────────────────────────────────────────────────────

    function fmt(num) {
        return '₱' + parseFloat(num).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function updateNavCount(count) {
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            el.textContent = count;
            el.classList.toggle('hidden', count === 0);
        });
    }

    function updateSummary(subtotal, total) {
        const s = document.getElementById('summary-subtotal');
        const t = document.getElementById('summary-total');
        if (s) s.textContent = fmt(subtotal);
        if (t) t.textContent = fmt(total);
    }

    function showEmptyCart() {
        const content = document.getElementById('cart-content');
        const wrapper = document.querySelector('#cart-page .max-w-5xl');

        if (content) content.remove();

        const empty = document.createElement('div');
        empty.className = 'text-center py-24';
        empty.innerHTML = `
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-2xl text-text mb-2" style="font-family: var(--font-display);">Your cart is empty</h3>
            <p class="text-muted text-sm mb-8">Add some beautiful arrangements to get started.</p>
            <a href="${BASE}/shop"
               class="bg-forest hover:bg-pine text-white font-medium px-8 py-3 rounded-full text-sm transition-all hover:-translate-y-px shadow-sm">
                Browse the Shop
            </a>`;
        wrapper.appendChild(empty);
    }

    async function cartRequest(url, productId, quantity = null) {
        const params = { _token: CSRF, product_id: productId };
        if (quantity !== null) params.quantity = quantity;

        const res = await fetch(BASE + url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams(params),
        });

        return res.json();
    }

    // ── Quantity buttons ─────────────────────────────────────────────────────

    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const productId = this.dataset.productId;
            const action    = this.dataset.action;
            const row       = document.querySelector(`.cart-row[data-product-id="${productId}"]`);
            const display   = row.querySelector('.qty-display');
            const incBtn    = row.querySelector('.qty-btn[data-action="increase"]');
            const current   = parseInt(display.textContent.trim());
            const newQty    = action === 'increase' ? current + 1 : current - 1;

            // Disable both buttons while request is in flight
            row.querySelectorAll('.qty-btn').forEach(b => b.disabled = true);

            try {
                const data = await cartRequest('/shop/cart/update', productId, newQty);

                if (data.success) {
                    if (data.data.removed) {
                        row.style.transition = 'opacity 0.3s, transform 0.3s';
                        row.style.opacity    = '0';
                        row.style.transform  = 'translateX(-10px)';
                        setTimeout(() => row.remove(), 300);
                    } else {
                        display.textContent = newQty;
                        row.querySelector('.item-total').textContent = fmt(data.data.item_total);
                        if (incBtn) incBtn.disabled = newQty >= parseInt(incBtn.dataset.max);
                        row.querySelector('.qty-btn[data-action="decrease"]').disabled = false;
                    }
                    updateSummary(data.data.subtotal, data.data.total);
                    updateNavCount(data.data.cart_count);
                    if (data.data.empty) setTimeout(showEmptyCart, 350);
                    Toast.success(data.message || 'Cart updated');
                } else {
                    row.querySelectorAll('.qty-btn').forEach(b => b.disabled = false);
                    Toast.error(data.message || 'Update failed');
                }
            } catch (e) {
                row.querySelectorAll('.qty-btn').forEach(b => b.disabled = false);
                Toast.error('Something went wrong.');
            }
        });
    });

    // ── Remove buttons ───────────────────────────────────────────────────────

    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const productId = this.dataset.productId;
            const row       = document.querySelector(`.cart-row[data-product-id="${productId}"]`);

            // Optimistic fade
            row.style.transition = 'opacity 0.3s, transform 0.3s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(-10px)';

            try {
                const data = await cartRequest('/shop/cart/remove', productId);

                if (data.success) {
                    setTimeout(() => row.remove(), 300);
                    updateSummary(data.data.subtotal, data.data.total);
                    updateNavCount(data.data.cart_count);
                    if (data.data.empty) setTimeout(showEmptyCart, 350);
                    Toast.info(data.message || 'Item removed');
                } else {
                    // Rollback fade
                    row.style.opacity   = '1';
                    row.style.transform = 'none';
                    Toast.error(data.message || 'Remove failed');
                }
            } catch (e) {
                row.style.opacity   = '1';
                row.style.transform = 'none';
                Toast.error('Something went wrong.');
            }
        });
    });

})();
</script>