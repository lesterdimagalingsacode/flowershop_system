<?php
// ─────────────────────────────────────────────────────────────────────────────
//  app/views/partials/retry_payment_modal.php
//
//  USAGE — include on any page that has a "Retry Payment" button:
//
//    <?php
//      $retryOrderId     = $order['id'];
//      $retryOrderNumber = $order['order_number'];
//      $retryTotal       = $order['total_amount'];
//      include __DIR__ . '/../partials/retry_payment_modal.php';
//    ?>
<!-- ── Retry Payment Modal ───────────────────────────────────────────────── -->
<div id="retryModal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="retryModalTitle">

    <!-- Backdrop -->
    <div id="retryModalBackdrop"
         class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-200 opacity-0"></div>

    <!-- Panel -->
    <div id="retryModalPanel"
         class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-border
                transition-all duration-200 scale-95 opacity-0">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-border">
            <div>
                <p class="text-xs tracking-[0.18em] uppercase text-gold font-medium mb-0.5">Secure Payment</p>
                <h2 id="retryModalTitle"
                    class="text-xl text-text"
                    style="font-family: var(--font-display);">Complete Your Order</h2>
            </div>
            <button onclick="RetryModal.close()"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-ivory transition text-muted hover:text-text"
                    aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Order summary strip -->
        <div class="px-6 py-3 bg-ivory flex items-center justify-between text-sm border-b border-border">
            <span class="text-muted">Order <span class="font-medium text-text"><?= e($retryOrderNumber) ?></span></span>
            <span class="font-bold text-forest">₱<?= number_format((float)$retryTotal, 2) ?></span>
        </div>

        <!-- Card form -->
        <form id="retryForm" method="POST"
              action="<?= APP_URL ?>/payment/retry/<?= (int)$retryOrderId ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="payment_method_id" id="retryPaymentMethodId">

            <div class="px-6 py-5 space-y-4">

                <!-- Card Number -->
                <div>
                    <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                        Card Number <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="retryCardNumber"
                           placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number"
                           class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm
                                  placeholder-muted focus:outline-none focus:border-forest focus:ring-2
                                  focus:ring-forest/20 transition">
                </div>

                <!-- Expiry + CVC -->
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                            MM / YY <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="retryCardExpiry"
                               placeholder="MM / YY" maxlength="7" autocomplete="cc-exp"
                               class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm
                                      placeholder-muted focus:outline-none focus:border-forest focus:ring-2
                                      focus:ring-forest/20 transition">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                            CVC <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="retryCardCvc"
                               placeholder="123" maxlength="4" autocomplete="cc-csc"
                               class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm
                                      placeholder-muted focus:outline-none focus:border-forest focus:ring-2
                                      focus:ring-forest/20 transition">
                    </div>
                </div>

                <!-- Cardholder Name -->
                <div>
                    <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                        Cardholder Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="retryCardName"
                           placeholder="Kim Lester Lumibao" autocomplete="cc-name"
                           class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm
                                  placeholder-muted focus:outline-none focus:border-forest focus:ring-2
                                  focus:ring-forest/20 transition">
                </div>

                <!-- Error box -->
                <div id="retryCardError" class="hidden p-3 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-xs text-red-700" id="retryCardErrorMsg"></p>
                </div>

            </div>

            <!-- Footer -->
            <div class="px-6 pb-6 space-y-3">
                <button type="button" id="retrySubmitBtn"
                        class="w-full bg-forest hover:bg-pine text-white font-medium text-sm px-6 py-3.5
                               rounded-full transition-all hover:-translate-y-px shadow-sm
                               disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-y-0">
                    Pay ₱<?= number_format((float)$retryTotal, 2) ?>
                </button>
                <button type="button" onclick="RetryModal.close()"
                        class="w-full bg-white border border-border hover:border-forest text-text font-medium
                               text-sm px-6 py-3.5 rounded-full transition-all">
                    Cancel
                </button>

                <p class="text-center text-xs text-muted pt-1">
                    🔒 Secured by PayMongo. No charges were made yet.
                </p>
            </div>
        </form>
    </div>
</div>

<script>
// ── Retry Modal JS ────────────────────────────────────────────────────────────
const RETRY_PAYMONGO_PK   = <?= json_encode(base64_encode(PAYMONGO_PUBLIC_KEY . ':')) ?>;
const RETRY_USER_EMAIL    = <?= json_encode(Session::user()['email'] ?? '') ?>;
const RETRY_USER_PHONE    = <?= json_encode(Session::user()['phone'] ?? '') ?>;

const RetryModal = (() => {
    const modal    = () => document.getElementById('retryModal');
    const backdrop = () => document.getElementById('retryModalBackdrop');
    const panel    = () => document.getElementById('retryModalPanel');

    function open() {
        modal().classList.remove('hidden');
        modal().classList.add('flex');
        // Trigger transition on next frame
        requestAnimationFrame(() => {
            backdrop().classList.replace('opacity-0', 'opacity-100');
            panel().classList.replace('scale-95', 'scale-100');
            panel().classList.replace('opacity-0', 'opacity-100');
        });
        document.body.style.overflow = 'hidden';
        document.getElementById('retryCardNumber').focus();
    }

    function close() {
        backdrop().classList.replace('opacity-100', 'opacity-0');
        panel().classList.replace('scale-100', 'scale-95');
        panel().classList.replace('opacity-100', 'opacity-0');
        setTimeout(() => {
            modal().classList.add('hidden');
            modal().classList.remove('flex');
            resetForm();
        }, 200);
        document.body.style.overflow = '';
    }

    function resetForm() {
        ['retryCardNumber', 'retryCardExpiry', 'retryCardCvc', 'retryCardName'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('retryPaymentMethodId').value = '';
        hideError();
        const btn = document.getElementById('retrySubmitBtn');
        btn.disabled    = false;
        btn.textContent = 'Pay ₱<?= number_format((float)$retryTotal, 2) ?>';
    }

    function showError(msg) {
        document.getElementById('retryCardErrorMsg').textContent = msg;
        document.getElementById('retryCardError').classList.remove('hidden');
    }

    function hideError() {
        document.getElementById('retryCardError').classList.add('hidden');
    }

    // Close on backdrop click
    document.getElementById('retryModalBackdrop').addEventListener('click', close);

    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal().classList.contains('hidden')) close();
    });

    // ── Card number formatting ────────────────
    document.getElementById('retryCardNumber').addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '').substring(0, 16);
        this.value = val.replace(/(.{4})/g, '$1 ').trim();
    });

    // ── Expiry formatting ─────────────────────
    document.getElementById('retryCardExpiry').addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '').substring(0, 4);
        if (val.length >= 2) val = val.substring(0, 2) + ' / ' + val.substring(2);
        this.value = val;
    });

    // ── Submit ────────────────────────────────
    document.getElementById('retrySubmitBtn').addEventListener('click', async function () {
        hideError();

        const cardNumber = document.getElementById('retryCardNumber').value.replace(/\s/g, '');
        const expiry     = document.getElementById('retryCardExpiry').value.replace(/\s/g, '').replace('/', '');
        const cvc        = document.getElementById('retryCardCvc').value.trim();
        const name       = document.getElementById('retryCardName').value.trim();
        const expMonth   = expiry.substring(0, 2);
        const expYear    = '20' + expiry.substring(2, 4);

        if (!cardNumber || !expiry || !cvc || !name) {
            showError('Please fill in all card details.');
            return;
        }

        const btn       = this;
        btn.disabled    = true;
        btn.textContent = 'Processing...';

        try {
            const pmResponse = await fetch('https://api.paymongo.com/v1/payment_methods', {
                method  : 'POST',
                headers : {
                    'Content-Type'  : 'application/json',
                    'Authorization' : 'Basic ' + RETRY_PAYMONGO_PK,
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
                                email : RETRY_USER_EMAIL,
                                phone : RETRY_USER_PHONE,
                            },
                        },
                    },
                }),
            });

            const pmData = await pmResponse.json();

            if (!pmResponse.ok) {
                const errMsg = pmData.errors?.[0]?.detail ?? 'Invalid card details.';
                showError(errMsg);
                btn.disabled    = false;
                btn.textContent = 'Pay ₱<?= number_format((float)$retryTotal, 2) ?>';
                return;
            }

            document.getElementById('retryPaymentMethodId').value = pmData.data.id;
            document.getElementById('retryForm').submit();

        } catch (err) {
            console.error('RetryModal PayMongo error:', err);
            showError('Network error. Please try again.');
            btn.disabled    = false;
            btn.textContent = 'Pay ₱<?= number_format((float)$retryTotal, 2) ?>';
        }
    });

    return { open, close };
})();
</script>