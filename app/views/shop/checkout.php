<?php $title = 'Checkout'; ?>

<div class="bg-cream min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-6">

        <!-- Header -->
        <div class="mb-8">
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-1">Final Step</p>
            <h1 class="text-4xl text-text" style="font-family: var(--font-display);">Checkout</h1>
        </div>

        <form method="POST" action="<?= APP_URL ?>/shop/checkout" id="checkoutForm">
            <?= csrf_field() ?>

            <?php foreach ($selectedIds as $id): ?>
                <input type="hidden" name="selected_items[]" value="<?= (int) $id ?>">
            <?php endforeach; ?>

            <input type="hidden" name="payment_method_id" id="paymentMethodId">

            <!-- Hidden promo fields — populated by JS on successful apply -->
            <input type="hidden" name="promo_code"     id="promoCodeHidden"    value="">
            <input type="hidden" name="promo_discount" id="promoDiscountHidden" value="0">

            <div class="flex gap-8 flex-col lg:flex-row">

                <!-- Left — Delivery details -->
                <div class="flex-1 space-y-5">
                    <div class="bg-white border border-border rounded-2xl p-6">
                        <h2 class="text-lg text-text mb-5 flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="text-xl">📍</span> Delivery Details
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">Recipient Name</label>
                                <input type="text" value="<?= e($user['first_name'] . ' ' . $user['last_name']) ?>" disabled
                                    class="w-full bg-ivory border border-border rounded-xl px-4 py-3 text-text text-sm opacity-70 cursor-not-allowed">
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
                                <input type="text" id="street" name="street"
                                    placeholder="e.g. 123 Quezon St."
                                    value="<?= e($_POST['street'] ?? '') ?>"
                                    required
                                    class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition">
                            </div>
                            <input type="hidden" id="delivery_address" name="delivery_address">
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                Order Notes <span class="text-muted normal-case tracking-normal">(optional)</span>
                            </label>
                            <textarea id="notes" name="notes" rows="2"
                                placeholder="Special instructions, delivery time preferences..."
                                class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition resize-none"
                            ><?= e($_POST['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Right — Order + Payment -->
                <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-5">

                    <!-- Payment Method -->
                    <div class="bg-white border border-border rounded-2xl p-6">
                        <h2 class="text-lg text-text mb-4 flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="text-xl">💳</span> Payment Method
                        </h2>

                        <div class="space-y-3">
                            <label class="flex items-center gap-4 p-4 border border-border rounded-xl cursor-pointer hover:border-forest transition has-[:checked]:border-forest has-[:checked]:bg-forest/5">
                                <input type="radio" name="payment_method" value="cod" checked class="accent-forest w-4 h-4">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="text-2xl">💵</span>
                                    <div>
                                        <p class="text-sm font-semibold text-text">Cash on Delivery</p>
                                        <p class="text-xs text-muted">Pay when your order arrives</p>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center gap-4 p-4 border border-border rounded-xl cursor-pointer hover:border-forest transition has-[:checked]:border-forest has-[:checked]:bg-forest/5">
                                <input type="radio" name="payment_method" value="online" class="accent-forest w-4 h-4">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="text-2xl">💳</span>
                                    <div>
                                        <p class="text-sm font-semibold text-text">Card / QR Ph</p>
                                        <p class="text-xs text-muted">Secure payment via PayMongo</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Card form -->
                        <div id="cardForm" class="hidden mt-4 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                    Card Number <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19"
                                    class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition">
                            </div>
                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                        MM / YY <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" id="cardExpiry" placeholder="MM / YY" maxlength="7"
                                        class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                        CVC <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" id="cardCvc" placeholder="123" maxlength="4"
                                        class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                    Cardholder Name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="cardName"
                                    placeholder="Kim Lester Lumibao"
                                    value="<?= e($user['first_name'] . ' ' . $user['last_name']) ?>"
                                    class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm placeholder-muted focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition">
                            </div>
                            <div id="cardError" class="hidden p-3 bg-red-50 border border-red-200 rounded-xl">
                                <p class="text-xs text-red-700" id="cardErrorMsg"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Order summary -->
                    <div class="bg-white border border-border rounded-2xl p-6">
                        <h2 class="text-xl text-text mb-5" style="font-family: var(--font-display);">Your Order</h2>

                        <!-- Cart items -->
                        <div class="space-y-3 mb-5">
                            <?php foreach ($cart as $item): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-ivory flex-shrink-0">
                                    <?php
                                    $imageUrl = $item['image']
                                        ? APP_URL . '/images/products/' . e($item['image'])
                                        : 'https://picsum.photos/seed/' . $item['product_id'] . '/100/100';
                                    ?>
                                    <img src="<?= $imageUrl ?>" alt="<?= e($item['name']) ?>" loading="lazy"
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

                        <!-- ── Promo Code ── -->
                        <div class="pt-4 border-t border-border mb-4">
                            <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                                Promo Code
                            </label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    id="promoInput"
                                    placeholder="e.g. SUMMER20"
                                    maxlength="50"
                                    autocomplete="off"
                                    class="flex-1 min-w-0 bg-white border border-border rounded-xl px-3 py-2.5 text-sm uppercase tracking-widest font-mono placeholder-muted placeholder:normal-case placeholder:tracking-normal focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/20 transition"
                                >
                                <button
                                    type="button"
                                    id="applyPromoBtn"
                                    class="flex-shrink-0 bg-forest hover:bg-pine text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition-all hover:-translate-y-px"
                                >
                                    Apply
                                </button>
                            </div>
                            <!-- Status message -->
                            <p id="promoMsg" class="hidden text-xs mt-1.5"></p>
                            <!-- Applied badge — shown after successful apply -->
                            <div id="promoBadge" class="hidden mt-2 flex items-center justify-between bg-green-50 border border-green-200 rounded-xl px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-green-600 text-sm">🏷️</span>
                                    <span id="promoBadgeCode" class="text-xs font-mono font-bold text-green-700 tracking-widest"></span>
                                    <span class="text-xs text-green-600">applied</span>
                                </div>
                                <button type="button" id="removePromoBtn" class="text-xs text-green-500 hover:text-red-500 transition font-medium ml-2">✕</button>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="space-y-2 pt-4 border-t border-border mb-5">
                            <div class="flex justify-between text-sm">
                                <span class="text-muted">Subtotal</span>
                                <span class="text-text">₱<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-muted">Delivery</span>
                                <span class="text-text"><?= $delivery > 0 ? '₱' . number_format($delivery, 2) : 'Free' ?></span>
                            </div>
                            <!-- Discount row — hidden until promo applied -->
                            <div id="discountRow" class="hidden flex justify-between text-sm">
                                <span class="text-green-600">Discount</span>
                                <span id="discountAmt" class="text-green-600 font-medium"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-border">
                                <span class="font-semibold text-text">Total</span>
                                <span id="totalDisplay" class="font-bold text-forest text-lg">₱<?= number_format($total, 2) ?></span>
                            </div>
                        </div>

                        <button type="button" id="placeOrderBtn"
                            class="w-full bg-forest hover:bg-pine text-white font-medium text-sm px-6 py-3.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                            Place Order
                        </button>

                        <p class="text-center text-xs text-muted mt-3">By placing your order you agree to our terms.</p>

                        <div class="mt-4 text-center">
                            <a href="<?= APP_URL ?>/shop/cart" class="text-xs text-muted hover:text-forest transition">
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
// ── PHP values passed to JS ───────────────────────────────────────────────
const PAYMONGO_PK  = <?= json_encode(base64_encode(PAYMONGO_PUBLIC_KEY . ':')) ?>;
const USER_EMAIL   = <?= json_encode($user['email'] ?? '') ?>;
const USER_PHONE   = <?= json_encode($user['phone']  ?? '') ?>;
const BASE_SUBTOTAL = <?= json_encode((float) $subtotal) ?>;
const BASE_DELIVERY = <?= json_encode((float) $delivery) ?>;
const APP_URL_JS    = <?= json_encode(APP_URL) ?>;
const CSRF_TOKEN    = <?= json_encode(csrf_token()) ?>;
const CSRF_NAME     = <?= json_encode(CSRF_TOKEN_NAME) ?>;
// ─────────────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {

    const form          = document.getElementById('checkoutForm');
    const placeOrderBtn = document.getElementById('placeOrderBtn');

    if (!form || !placeOrderBtn) {
        console.error('Checkout: form or button not found in DOM');
        return;
    }

    console.log('Checkout JS loaded ✅');

    // ── Block Enter key from native submit ────
    form.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') e.preventDefault();
    });

    // ── Debug: catch any native submit ────────
    form.addEventListener('submit', function () {
        console.warn('NATIVE SUBMIT — payment_method_id:', document.getElementById('paymentMethodId').value);
    });

    // ── Init address picker ───────────────────
    AddressPicker.init({
        municipalityEl : document.getElementById('municipality'),
        barangayEl     : document.getElementById('barangay'),
        streetEl       : document.getElementById('street'),
        outputEl       : document.getElementById('delivery_address'),
        provinceCode   : '037700000',
        provinceName   : 'Aurora',
        regionName     : 'Region III (Central Luzon)',
        defaultMuni    : 'baler',
        baseUrl        : APP_URL_JS,
    });

    // ── Payment method toggle ─────────────────
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const isOnline = this.value === 'online';
            document.getElementById('cardForm').classList.toggle('hidden', !isOnline);
            placeOrderBtn.textContent = isOnline ? 'Continue to Payment →' : 'Place Order';
        });
    });

    // ── Card number formatting ────────────────
    document.getElementById('cardNumber').addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '').substring(0, 16);
        this.value = val.replace(/(.{4})/g, '$1 ').trim();
    });

    // ── Expiry formatting ─────────────────────
    document.getElementById('cardExpiry').addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '').substring(0, 4);
        if (val.length >= 2) val = val.substring(0, 2) + ' / ' + val.substring(2);
        this.value = val;
    });

    // ═══════════════════════════════════════════
    //  PROMO CODE
    // ═══════════════════════════════════════════
    const promoInput        = document.getElementById('promoInput');
    const applyPromoBtn     = document.getElementById('applyPromoBtn');
    const removePromoBtn    = document.getElementById('removePromoBtn');
    const promoMsg          = document.getElementById('promoMsg');
    const promoBadge        = document.getElementById('promoBadge');
    const promoBadgeCode    = document.getElementById('promoBadgeCode');
    const discountRow       = document.getElementById('discountRow');
    const discountAmt       = document.getElementById('discountAmt');
    const totalDisplay      = document.getElementById('totalDisplay');
    const promoCodeHidden   = document.getElementById('promoCodeHidden');
    const promoDiscountHid  = document.getElementById('promoDiscountHidden');

    let appliedDiscount = 0;

    function formatPHP(amount) {
        return '₱' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function refreshTotal() {
        const newTotal = Math.max(0, BASE_SUBTOTAL + BASE_DELIVERY - appliedDiscount);
        totalDisplay.textContent = formatPHP(newTotal);
    }

    function setPromoMsg(text, isError) {
        promoMsg.textContent = text;
        promoMsg.className   = 'text-xs mt-1.5 ' + (isError ? 'text-red-500' : 'text-green-600');
        promoMsg.classList.remove('hidden');
    }

    function clearPromoMsg() {
        promoMsg.classList.add('hidden');
        promoMsg.textContent = '';
    }

    function applyPromoToUI(code, discount) {
        appliedDiscount = discount;

        // Badge
        promoBadgeCode.textContent = code;
        promoBadge.classList.remove('hidden');

        // Discount row
        discountAmt.textContent = '−' + formatPHP(discount);
        discountRow.classList.remove('hidden');

        // Hidden fields for form submit
        promoCodeHidden.value  = code;
        promoDiscountHid.value = discount;

        // Updated total
        refreshTotal();

        // Lock input
        promoInput.disabled       = true;
        applyPromoBtn.disabled    = true;
        applyPromoBtn.textContent = 'Applied ✓';
        applyPromoBtn.classList.remove('bg-forest', 'hover:bg-pine', 'hover:-translate-y-px');
        applyPromoBtn.classList.add('bg-gray-300', 'cursor-not-allowed');

        clearPromoMsg();
    }

    function resetPromoUI() {
        appliedDiscount = 0;

        promoBadge.classList.add('hidden');
        discountRow.classList.add('hidden');
        discountAmt.textContent = '';

        promoCodeHidden.value  = '';
        promoDiscountHid.value = '0';

        promoInput.disabled       = false;
        promoInput.value          = '';
        applyPromoBtn.disabled    = false;
        applyPromoBtn.textContent = 'Apply';
        applyPromoBtn.classList.add('bg-forest', 'hover:bg-pine', 'hover:-translate-y-px');
        applyPromoBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');

        refreshTotal();
        clearPromoMsg();
    }

    // Apply button click
    applyPromoBtn.addEventListener('click', async function () {
        const code = promoInput.value.trim().toUpperCase();

        if (!code) {
            setPromoMsg('Please enter a promo code.', true);
            return;
        }

        applyPromoBtn.disabled    = true;
        applyPromoBtn.textContent = '...';

        try {
            const body = new URLSearchParams({
                [CSRF_NAME]: CSRF_TOKEN,
                code       : code,
                subtotal   : BASE_SUBTOTAL,
            });

            const res  = await fetch(APP_URL_JS + '/promo/validate', {
                method  : 'POST',
                headers : { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body    : body.toString(),
            });

            const data = await res.json();

            if (data.success) {
                applyPromoToUI(data.code, data.discount);
            } else {
                setPromoMsg(data.message ?? 'Invalid promo code.', true);
                applyPromoBtn.disabled    = false;
                applyPromoBtn.textContent = 'Apply';
            }

        } catch (err) {
            console.error('Promo validate error:', err);
            setPromoMsg('Network error. Please try again.', true);
            applyPromoBtn.disabled    = false;
            applyPromoBtn.textContent = 'Apply';
        }
    });

    // Enter key in promo input
    promoInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); applyPromoBtn.click(); }
    });

    // Auto-uppercase as user types
    promoInput.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });

    // Remove promo
    removePromoBtn.addEventListener('click', function () {
        resetPromoUI();
    });

    // ═══════════════════════════════════════════
    //  PLACE ORDER
    // ═══════════════════════════════════════════
    placeOrderBtn.addEventListener('click', async function () {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

        // COD path
        if (paymentMethod !== 'online') {
            if (!form.checkValidity()) { form.reportValidity(); return; }
            console.log('COD: submitting...');
            form.submit();
            return;
        }

        // Online path
        const btn = this;
        btn.disabled    = true;
        btn.textContent = 'Processing...';
        document.getElementById('cardError').classList.add('hidden');

        const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const expiry     = document.getElementById('cardExpiry').value.replace(/\s/g, '').replace('/', '');
        const cvc        = document.getElementById('cardCvc').value;
        const name       = document.getElementById('cardName').value;
        const expMonth   = expiry.substring(0, 2);
        const expYear    = '20' + expiry.substring(2, 4);

        if (!cardNumber || !expiry || !cvc || !name) {
            showCardError('Please fill in all card details.');
            btn.disabled = false; btn.textContent = 'Continue to Payment →';
            return;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            btn.disabled = false; btn.textContent = 'Continue to Payment →';
            return;
        }

        try {
            console.log('Calling PayMongo /payment_methods...');
            const pmResponse = await fetch('https://api.paymongo.com/v1/payment_methods', {
                method  : 'POST',
                headers : {
                    'Content-Type'  : 'application/json',
                    'Authorization' : 'Basic ' + PAYMONGO_PK,
                },
                body: JSON.stringify({
                    data: {
                        attributes: {
                            type    : 'card',
                            details : {
                                card_number : cardNumber,
                                exp_month   : parseInt(expMonth),
                                exp_year    : parseInt(expYear),
                                cvc         : cvc,
                            },
                            billing : {
                                name  : name,
                                email : USER_EMAIL,
                                phone : USER_PHONE,
                            },
                        },
                    },
                }),
            });

            const pmData = await pmResponse.json();
            console.log('PayMongo response:', pmData);

            if (!pmResponse.ok) {
                const errMsg = pmData.errors?.[0]?.detail ?? 'Invalid card details.';
                showCardError(errMsg);
                btn.disabled = false; btn.textContent = 'Continue to Payment →';
                return;
            }

            console.log('Payment method created:', pmData.data.id);
            document.getElementById('paymentMethodId').value = pmData.data.id;
            form.submit();

        } catch (err) {
            console.error('PayMongo fetch error:', err);
            showCardError('Network error. Please try again.');
            btn.disabled = false; btn.textContent = 'Continue to Payment →';
        }
    });

    function showCardError(msg) {
        document.getElementById('cardErrorMsg').textContent = msg;
        document.getElementById('cardError').classList.remove('hidden');
    }

}); // end DOMContentLoaded
</script>