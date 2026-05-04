// ─────────────────────────────────────────────
//  public/js/app.js — Global App Behaviors
// ─────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {

    // ── Mobile menu toggle ────────────────────
    const menuBtn  = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    menuBtn?.addEventListener('click', () => {
        mobileMenu?.classList.toggle('hidden');
    });

    // Close mobile menu on outside click
    document.addEventListener('click', (e) => {
        if (!menuBtn?.contains(e.target) && !mobileMenu?.contains(e.target)) {
            mobileMenu?.classList.add('hidden');
        }
    });

    // ── Flash message auto-dismiss ────────────
    const flashContainer = document.getElementById('flash-container');
    if (flashContainer) {
        setTimeout(() => {
            flashContainer.querySelectorAll(':scope > div').forEach(el => {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity    = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
    }

    // ── Confirm dialogs ───────────────────────
    // Any button/link with data-confirm="message" will prompt before proceeding
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            const message = el.dataset.confirm || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // ── Auto-submit forms on change ───────────
    // Any form with data-autosubmit will submit when any input changes
    document.querySelectorAll('form[data-autosubmit]').forEach(form => {
        form.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('change', () => form.submit());
        });
    });

    // ── Quantity input controls ───────────────
    // For + / - buttons on quantity inputs
    document.querySelectorAll('[data-qty-input]').forEach(wrapper => {
        const input   = wrapper.querySelector('input[type="number"]');
        const minusBtn = wrapper.querySelector('[data-qty-minus]');
        const plusBtn  = wrapper.querySelector('[data-qty-plus]');

        minusBtn?.addEventListener('click', () => {
            const min = parseInt(input.min ?? 1);
            if (parseInt(input.value) > min) {
                input.value = parseInt(input.value) - 1;
                input.dispatchEvent(new Event('change'));
            }
        });

        plusBtn?.addEventListener('click', () => {
            const max = input.max ? parseInt(input.max) : Infinity;
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    // ── Active nav link highlight ─────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('nav a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.startsWith(href) && href !== '/') {
            link.classList.add('text-forest');
            link.classList.remove('text-muted');
        }
    });

    // ── Image lazy load fallback ──────────────
    document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        img.addEventListener('error', () => {
            img.src = 'https://picsum.photos/seed/' + Math.floor(Math.random() * 100) + '/400/300';
        });
    });

});

// ── Global CSRF helper ────────────────────────
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

// ── Global fetch helper with CSRF ─────────────
async function apiFetch(url, options = {}) {
    const defaults = {
        headers: {
            'Content-Type': 'application/json',
            'Accept':       'application/json',
            'X-CSRF-Token': getCsrfToken(),
        },
    };
    const config = { ...defaults, ...options };
    config.headers = { ...defaults.headers, ...(options.headers ?? {}) };

    const res  = await fetch(url, config);
    const data = await res.json();

    if (!res.ok) {
        throw new Error(data.message ?? 'Request failed');
    }

    return data;
}