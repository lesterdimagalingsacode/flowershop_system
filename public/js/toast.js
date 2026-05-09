// ─────────────────────────────────────────────
//  public/js/toast.js — Toast Notifications
// ─────────────────────────────────────────────

const Toast = (() => {

    // ── Create container if not exists ────────
    function getContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = `
                position: fixed;
                bottom: 1.25rem;
                left: 50%;
                transform: translateX(-50%);
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                width: calc(100% - 2rem);
                max-width: 360px;
                pointer-events: none;
            `;
            document.body.appendChild(container);
        }
        return container;
    }

    // ── Type styles ───────────────────────────
    const styles = {
        success: {
            bg:     '#f0faf4',
            border: '#86efac',
            text:   '#166534',
            icon:   '✓',
        },
        error: {
            bg:     '#fff1f2',
            border: '#fca5a5',
            text:   '#991b1b',
            icon:   '✕',
        },
        warning: {
            bg:     '#fffbeb',
            border: '#fcd34d',
            text:   '#92400e',
            icon:   '!',
        },
        info: {
            bg:     '#f0f9ff',
            border: '#7dd3fc',
            text:   '#075985',
            icon:   'i',
        },
    };

    // ── Show a toast ──────────────────────────
    function show(message, type = 'info', duration = 4000) {
        const container = getContainer();
        const style     = styles[type] ?? styles.info;

        const toast = document.createElement('div');
        toast.style.cssText = `
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            background: ${style.bg};
            border: 1px solid ${style.border};
            border-radius: 0.75rem;
            color: ${style.text};
            font-size: 0.875rem;
            font-family: var(--font-body, sans-serif);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            cursor: pointer;
            pointer-events: auto;
            width: 100%;
            box-sizing: border-box;
            word-break: break-word;
        `;

        toast.innerHTML = `
            <span style="
                width: 1.25rem;
                height: 1.25rem;
                border-radius: 50%;
                background: ${style.border};
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.65rem;
                font-weight: 700;
                flex-shrink: 0;
                color: ${style.text};
            ">${style.icon}</span>
            <span style="flex: 1; line-height: 1.4;">${message}</span>
            <button style="
                opacity: 0.5;
                background: none;
                border: none;
                cursor: pointer;
                font-size: 0.75rem;
                color: ${style.text};
                padding: 0;
                flex-shrink: 0;
            " aria-label="Dismiss">✕</button>
        `;

        // Dismiss on click
        toast.addEventListener('click', () => dismiss(toast));

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.style.opacity   = '1';
                toast.style.transform = 'translateY(0)';
            });
        });

        // Auto dismiss
        let timer = setTimeout(() => dismiss(toast), duration);

        // Cancel auto dismiss on hover
        toast.addEventListener('mouseenter', () => clearTimeout(timer));
        toast.addEventListener('mouseleave', () => {
            timer = setTimeout(() => dismiss(toast), 1500);
        });

        return toast;
    }

    // ── Dismiss a toast ───────────────────────
    function dismiss(toast) {
        toast.style.opacity   = '0';
        toast.style.transform = 'translateY(8px)';
        setTimeout(() => toast.remove(), 300);
    }

    // ── Shorthand methods ─────────────────────
    return {
        show,
        success: (msg, duration) => show(msg, 'success', duration),
        error:   (msg, duration) => show(msg, 'error',   duration),
        warning: (msg, duration) => show(msg, 'warning', duration),
        info:    (msg, duration) => show(msg, 'info',    duration),
    };

})();

// ── Auto-show flash messages as toasts ────────
document.addEventListener('DOMContentLoaded', () => {
    const flashContainer = document.getElementById('flash-container');
    if (!flashContainer) return;

    flashContainer.querySelectorAll(':scope > div').forEach(el => {
        const text = el.querySelector('span:nth-child(2)')?.textContent?.trim();
        if (!text) return;

        let type = 'info';
        if (el.classList.contains('bg-green-50'))  type = 'success';
        if (el.classList.contains('bg-red-50'))    type = 'error';
        if (el.classList.contains('bg-yellow-50')) type = 'warning';

        Toast.show(text, type);
        el.remove();
    });

    if (!flashContainer.children.length) {
        flashContainer.remove();
    }
});