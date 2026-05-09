<?php $title = 'Checkout'; ?>

<div class="bg-cream min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-6">

        <!-- Header -->
        <div class="mb-8">
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-1">Final Step</p>
            <h1 class="text-4xl text-text" style="font-family: var(--font-display);">Checkout</h1>
        </div>

        <form method="POST" action="<?= APP_URL ?>/shop/checkout">
            <?= csrf_field() ?>

            <?php foreach ($selectedIds as $id): ?>
                <input type="hidden" name="selected_items[]" value="<?= (int) $id ?>">
            <?php endforeach; ?>

            <div class="flex gap-8 flex-col lg:flex-row">

                <!-- Left — Delivery details -->
                <div class="flex-1 space-y-5">

                    <div class="bg-white border border-border rounded-2xl p-6">
                        <h2 class="text-lg text-text mb-5 flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="text-xl">📍</span> Delivery Details
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                    Recipient Name
                                </label>
                                <input
                                    type="text"
                                    value="<?= e($user['first_name'] . ' ' . $user['last_name']) ?>"
                                    disabled
                                    class="w-full bg-ivory border border-border rounded-xl px-4 py-3 text-text text-sm opacity-70 cursor-not-allowed"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">Region</label>
                                <input type="text" value="Region III (Central Luzon)" disabled
                                    class="w-full bg-ivory border border-border rounded-xl px-4 py-3 text-text text-sm opacity-70 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">Province</label>
                                <input type="text" value="Aurora" disabled
                                    class="w-full bg-ivory border border-border rounded-xl px-4 py-3 text-text text-sm opacity-70 cursor-not-allowed">
                            </div>

                            <div>
                                <label for="municipality" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                    Municipality <span class="text-red-400">*</span>
                                </label>
                                <select id="municipality" name="municipality" required
                                    class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition">
                                    <option value="">— Loading municipalities... —</option>
                                </select>
                            </div>

                            <div>
                                <label for="barangay" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                    Barangay <span class="text-red-400">*</span>
                                </label>
                                <select id="barangay" name="barangay" required disabled
                                    class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">— Select municipality first —</option>
                                </select>
                            </div>

                            <div>
                                <label for="street" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                    House No. / Street <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="street"
                                    name="street"
                                    placeholder="e.g. 123 Quezon St."
                                    value="<?= e($_POST['street'] ?? '') ?>"
                                    required
                                    class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                                >
                            </div>

                            <input type="hidden" id="delivery_address" name="delivery_address">
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                Order Notes <span class="text-muted normal-case tracking-normal">(optional)</span>
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="2"
                                placeholder="Special instructions, delivery time preferences..."
                                class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition resize-none"
                            ><?= e($_POST['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                </div>

                <!-- Right — Order + Payment -->
                <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-5">

                    <!-- Payment Method — moved above order summary so user sees it first -->
                    <div class="bg-white border border-border rounded-2xl p-6">
                        <h2 class="text-lg text-text mb-4 flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="text-xl">💳</span> Payment Method
                        </h2>

                        <div class="space-y-3" id="paymentOptions">
                            <!-- COD -->
                            <label class="flex items-center gap-4 p-4 border border-border rounded-xl cursor-pointer hover:border-forest transition has-[:checked]:border-forest has-[:checked]:bg-forest/5">
                                <input type="radio" name="payment_method" value="cod" checked
                                    class="accent-forest w-4 h-4">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="text-2xl">💵</span>
                                    <div>
                                        <p class="text-sm font-semibold text-text">Cash on Delivery</p>
                                        <p class="text-xs text-muted">Pay when your order arrives</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Online Payment via PayMongo -->
                            <label class="flex items-center gap-4 p-4 border border-border rounded-xl cursor-pointer hover:border-forest transition has-[:checked]:border-forest has-[:checked]:bg-forest/5">
                                <input type="radio" name="payment_method" value="online"
                                    class="accent-forest w-4 h-4">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="text-2xl">💳</span>
                                    <div>
                                        <p class="text-sm font-semibold text-text">Card / QR Ph</p>
                                        <p class="text-xs text-muted">Secure payment via PayMongo</p>
                                    </div>
                                </div>
                                <img src="https://assets.paymongo.com/paymongo-assets/pm-logo-sm.png"
                                     alt="PayMongo"
                                     class="h-5 opacity-60"
                                     onerror="this.style.display='none'">
                            </label>
                        </div>

                        <!-- Online payment note -->
                        <div id="onlineNote" class="hidden mt-3 p-3 bg-blue-50 border border-blue-100 rounded-xl">
                            <p class="text-xs text-blue-700">
                                🔒 You'll be redirected to PayMongo's secure payment page after placing your order.
                                Accepted: <strong>Credit/Debit Card</strong> and <strong>QR Ph</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- Your Order summary -->
                    <div class="bg-white border border-border rounded-2xl p-6">

                        <h2 class="text-xl text-text mb-5" style="font-family: var(--font-display);">Your Order</h2>

                        <div class="space-y-3 mb-5">
                            <?php foreach ($cart as $item): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-ivory flex-shrink-0">
                                    <?php
                                    $imageUrl = $item['image']
                                        ? APP_URL . '/images/products/' . e($item['image'])
                                        : 'https://picsum.photos/seed/' . $item['product_id'] . '/100/100';
                                    ?>
                                    <img src="<?= $imageUrl ?>" alt="<?= e($item['name']) ?>"
                                         loading="lazy"
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-text line-clamp-1"><?= e($item['name']) ?></p>
                                    <p class="text-xs text-muted">x<?= $item['quantity'] ?></p>
                                </div>
                                <p class="text-xs font-bold text-text flex-shrink-0">
                                    ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="space-y-2 pt-4 border-t border-border mb-5">
                            <div class="flex justify-between text-sm">
                                <span class="text-muted">Subtotal</span>
                                <span class="text-text">₱<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-muted">Delivery</span>
                                <span class="text-text">
                                    <?= $delivery > 0 ? '₱' . number_format($delivery, 2) : 'Free' ?>
                                </span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-border">
                                <span class="font-semibold text-text">Total</span>
                                <span class="font-bold text-forest text-lg">₱<?= number_format($total, 2) ?></span>
                            </div>
                        </div>

                        <!-- Dynamic button label based on payment method -->
                        <button type="submit" id="placeOrderBtn"
                            class="w-full bg-forest hover:bg-pine text-white font-medium text-sm px-6 py-3.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                            Place Order
                        </button>

                        <p class="text-center text-xs text-muted mt-3">
                            By placing your order you agree to our terms.
                        </p>

                        <div class="mt-4 text-center">
                            <a href="<?= APP_URL ?>/shop/cart"
                               class="text-xs text-muted hover:text-forest transition">
                                ← Back to Cart
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </form>
    </div>
</div>

<script src="<?= APP_URL ?>/js/psgc.js"></script>
<script>
AddressPicker.init({
    municipalityEl : document.getElementById('municipality'),
    barangayEl     : document.getElementById('barangay'),
    streetEl       : document.getElementById('street'),
    outputEl       : document.getElementById('delivery_address'),
    form           : document.querySelector('form'),
    provinceCode   : '037700000',
    provinceName   : 'Aurora',
    regionName     : 'Region III (Central Luzon)',
    defaultMuni    : 'baler',
    baseUrl        : '<?= APP_URL ?>',
});

// Payment method toggle
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isOnline = this.value === 'online';
        document.getElementById('onlineNote').classList.toggle('hidden', !isOnline);
        document.getElementById('placeOrderBtn').textContent = isOnline
            ? 'Continue to Payment →'
            : 'Place Order';
    });
});
</script>