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

                <!-- Select all bar -->
                <div class="flex items-center justify-between mb-3 px-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-muted">
                        <input type="checkbox" id="select-all"
                            class="w-4 h-4 rounded border-border accent-forest cursor-pointer">
                        <span>Select all</span>
                    </label>
                    <span class="text-xs text-muted" id="selected-count">0 item(s) selected</span>
                </div>

                <div class="bg-white border border-border rounded-2xl overflow-hidden" id="cart-items">

                    <?php foreach ($cart as $item): ?>
                    <div class="cart-row border-b border-border last:border-b-0"
                         data-product-id="<?= $item['product_id'] ?>"
                         data-price="<?= $item['price'] ?>">

                        <!-- Mobile layout: stacked -->
                        <div class="flex items-start gap-3 p-4">

                            <!-- Checkbox -->
                            <div class="flex-shrink-0 pt-1">
                                <input type="checkbox"
                                    class="item-checkbox w-4 h-4 rounded border-border accent-forest cursor-pointer"
                                    data-product-id="<?= $item['product_id'] ?>"
                                    data-price="<?= $item['price'] ?>">
                            </div>

                            <!-- Image -->
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden flex-shrink-0 bg-ivory">
                                <?php
                                $imageUrl = $item['image']
                                    ? APP_URL . '/images/products/' . e($item['image'])
                                    : 'https://picsum.photos/seed/' . $item['product_id'] . '/200/200';
                                ?>
                                <img src="<?= $imageUrl ?>" alt="<?= e($item['name']) ?>"
                                     class="w-full h-full object-cover">
                            </div>

                            <!-- Details + controls -->
                            <div class="flex-1 min-w-0">

                                <!-- Name + remove -->
                                <div class="flex items-start justify-between gap-2">
                                    <a href="<?= APP_URL ?>/shop/<?= e($item['slug']) ?>" class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-text hover:text-forest transition text-sm sm:text-base leading-snug line-clamp-2"
                                            style="font-family: var(--font-display);">
                                            <?= e($item['name']) ?>
                                        </h3>
                                    </a>
                                    <button type="button"
                                        class="remove-btn text-muted hover:text-red-500 transition p-1 flex-shrink-0 -mt-0.5"
                                        data-product-id="<?= $item['product_id'] ?>"
                                        title="Remove item">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Price -->
                                <p class="text-sm text-forest font-bold mt-1">
                                    ₱<?= number_format($item['price'], 2) ?>
                                </p>

                                <!-- Quantity + item total row -->
                                <div class="flex items-center justify-between mt-2 gap-2 flex-wrap">

                                    <!-- Quantity controls -->
                                    <div class="flex items-center border border-border rounded-full overflow-hidden">
                                        <button type="button"
                                            class="qty-btn w-8 h-8 flex items-center justify-center text-muted hover:text-forest hover:bg-cream transition"
                                            data-product-id="<?= $item['product_id'] ?>"
                                            data-action="decrease">
                                            −
                                        </button>
                                        <span class="qty-display w-8 text-center text-sm font-medium text-text">
                                            <?= $item['quantity'] ?>
                                        </span>
                                        <button type="button"
                                            class="qty-btn w-8 h-8 flex items-center justify-center text-muted hover:text-forest hover:bg-cream transition disabled:opacity-30"
                                            data-product-id="<?= $item['product_id'] ?>"
                                            data-action="increase"
                                            data-max="<?= $item['stock'] ?>"
                                            <?= $item['quantity'] >= $item['stock'] ? 'disabled' : '' ?>>
                                            +
                                        </button>
                                    </div>

                                    <!-- Item total -->
                                    <p class="text-sm font-bold text-text item-total">
                                        ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                    </p>

                                </div>
                            </div>
                        </div>

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
                    <h2 class="text-xl text-text mb-1" style="font-family: var(--font-display);">Order Summary</h2>
                    <p class="text-xs text-muted mb-5" id="summary-selection-note">Select items to checkout</p>

                    <div class="space-y-3 mb-5">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Subtotal</span>
                            <span class="text-text font-medium" id="summary-subtotal">₱0.00</span>
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
                            <span class="font-bold text-forest text-lg" id="summary-total">₱0.00</span>
                        </div>
                    </div>

                    <!-- Checkout button — disabled until items selected -->
                    <button type="button" id="checkout-btn"
                        disabled
                        onclick="proceedCheckout()"
                        class="block w-full bg-forest hover:bg-pine disabled:bg-border disabled:text-muted disabled:cursor-not-allowed text-white font-medium text-sm text-center px-6 py-3.5 rounded-full transition-all hover:-translate-y-px shadow-sm disabled:shadow-none disabled:hover:translate-y-0">
                        Proceed to Checkout
                    </button>

                    <!-- Hidden form for checkout with selected items -->
                    <form method="POST" action="<?= APP_URL ?>/shop/checkout" id="checkout-form" class="hidden">
                        <?= csrf_field() ?>
                        <div id="checkout-items-input"></div>
                    </form>

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
    const CSRF     = '<?= csrf_token() ?>';
    const BASE     = '<?= APP_URL ?>';
    const DELIVERY = <?= (float)($delivery ?? 0) ?>;

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

    // ── Selective checkout ───────────────────────────────────────────────────

    function getCheckedIds() {
        return [...document.querySelectorAll('.item-checkbox:checked')]
            .map(cb => cb.dataset.productId);
    }

    function recalcSelected() {
        const checked  = document.querySelectorAll('.item-checkbox:checked');
        const count    = checked.length;
        const note     = document.getElementById('summary-selection-note');
        const btn      = document.getElementById('checkout-btn');
        const countEl  = document.getElementById('selected-count');
        const selectAll = document.getElementById('select-all');
        const allBoxes = document.querySelectorAll('.item-checkbox');

        // Subtotal of selected items only
        let subtotal = 0;
        checked.forEach(cb => {
            const row = document.querySelector(`.cart-row[data-product-id="${cb.dataset.productId}"]`);
            const qty = parseInt(row.querySelector('.qty-display').textContent.trim());
            subtotal += parseFloat(cb.dataset.price) * qty;
        });

        const total = subtotal + DELIVERY;

        updateSummary(subtotal, total);

        if (countEl) countEl.textContent = `${count} item(s) selected`;
        if (note)    note.textContent    = count > 0 ? `${count} item(s) selected` : 'Select items to checkout';
        if (btn)     btn.disabled        = count === 0;

        // Sync select-all state
        if (selectAll) {
            selectAll.indeterminate = count > 0 && count < allBoxes.length;
            selectAll.checked       = count === allBoxes.length && allBoxes.length > 0;
        }
    }

    // Select all toggle
    const selectAllBox = document.getElementById('select-all');
    if (selectAllBox) {
        selectAllBox.addEventListener('change', function () {
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            recalcSelected();
        });
    }

    // Individual checkbox
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('change', recalcSelected);
    });

    // Highlight selected rows
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const row = document.querySelector(`.cart-row[data-product-id="${this.dataset.productId}"]`);
            if (row) row.style.background = this.checked ? 'var(--color-ivory, #faf9f6)' : '';
        });
    });

    // Run once to set initial state
    recalcSelected();

    // ── Checkout with selected items ─────────────────────────────────────────

    window.proceedCheckout = function () {
        const ids = getCheckedIds();
        if (!ids.length) return;

        const container = document.getElementById('checkout-items-input');
        container.innerHTML = '';
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'selected_items[]';
            input.value = id;
            container.appendChild(input);
        });

        document.getElementById('checkout-form').submit();
    };

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

            row.querySelectorAll('.qty-btn').forEach(b => b.disabled = true);

            try {
                const data = await cartRequest('/shop/cart/update', productId, newQty);

                if (data.success) {
                    if (data.data.removed) {
                        row.style.transition = 'opacity 0.3s, transform 0.3s';
                        row.style.opacity    = '0';
                        row.style.transform  = 'translateX(-10px)';
                        setTimeout(() => {
                            row.remove();
                            recalcSelected();
                        }, 300);
                    } else {
                        display.textContent = newQty;
                        row.querySelector('.item-total').textContent = fmt(data.data.item_total);
                        if (incBtn) incBtn.disabled = newQty >= parseInt(incBtn.dataset.max);
                        row.querySelector('.qty-btn[data-action="decrease"]').disabled = false;
                        recalcSelected(); // update selected subtotal
                    }
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

            row.style.transition = 'opacity 0.3s, transform 0.3s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(-10px)';

            try {
                const data = await cartRequest('/shop/cart/remove', productId);

                if (data.success) {
                    setTimeout(() => {
                        row.remove();
                        recalcSelected();
                    }, 300);
                    updateNavCount(data.data.cart_count);
                    if (data.data.empty) setTimeout(showEmptyCart, 350);
                    Toast.info(data.message || 'Item removed');
                } else {
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