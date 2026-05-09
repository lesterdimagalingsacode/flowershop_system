<?php
// app/views/admin/inquiry-detail.php
// Layout: admin
?>

<div class="max-w-3xl mx-auto px-4 py-8">

    <!-- Back + Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="/admin/inquiries" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">Inquiry #<?= $message['id'] ?></h1>
            <p class="text-sm text-gray-400"><?= date('F j, Y \a\t g:i A', strtotime($message['created_at'])) ?></p>
        </div>
        <!-- Status toggle -->
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Status:</span>
            <button
                id="statusToggleBtn"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition
                    <?= $message['status'] === 'resolved'
                        ? 'bg-green-100 text-green-700 hover:bg-green-200'
                        : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' ?>"
                data-message-id="<?= $message['id'] ?>"
                data-current="<?= $message['status'] ?>"
            >
                <span class="status-dot w-1.5 h-1.5 rounded-full <?= $message['status'] === 'resolved' ? 'bg-green-500' : 'bg-yellow-500' ?>"></span>
                <span class="status-label"><?= ucfirst($message['status']) ?></span>
            </button>
        </div>
    </div>

    <!-- Customer info card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Customer</h2>
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-bold text-lg flex-shrink-0">
                <?= strtoupper(substr($message['customer_name'], 0, 1)) ?>
            </div>
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                <div>
                    <p class="text-gray-400 text-xs">Name</p>
                    <p class="font-medium text-gray-900"><?= htmlspecialchars($message['customer_name']) ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Email</p>
                    <a href="mailto:<?= htmlspecialchars($message['customer_email']) ?>"
                       class="text-pink-500 hover:underline font-medium">
                        <?= htmlspecialchars($message['customer_email']) ?>
                    </a>
                </div>
                <?php if ($message['order_ref']): ?>
                    <div>
                        <p class="text-gray-400 text-xs">Order</p>
                        <a href="/admin/orders/<?= $message['order_ref'] ?>"
                           class="font-mono text-pink-500 hover:underline font-medium">
                            #<?= $message['order_ref'] ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Thread -->
    <div class="space-y-4 mb-6">

        <!-- Original message -->
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-semibold text-sm flex-shrink-0 mt-1">
                <?= strtoupper(substr($message['customer_name'], 0, 1)) ?>
            </div>
            <div class="flex-1">
                <div class="bg-white rounded-2xl rounded-tl-sm border border-gray-200 shadow-sm px-4 py-3">
                    <p class="text-sm text-gray-800 whitespace-pre-wrap"><?= htmlspecialchars($message['message']) ?></p>
                </div>
                <p class="text-xs text-gray-400 mt-1 ml-1">
                    <?= htmlspecialchars($message['customer_name']) ?> · <?= date('M j, g:i A', strtotime($message['created_at'])) ?>
                </p>
            </div>
        </div>

        <!-- Existing replies -->
        <?php foreach ($replies as $reply): ?>
            <div class="flex gap-3 justify-end" id="reply-<?= $reply['id'] ?>">
                <div class="flex-1 max-w-lg">
                    <div class="bg-pink-500 text-white rounded-2xl rounded-tr-sm px-4 py-3">
                        <p class="text-sm whitespace-pre-wrap"><?= htmlspecialchars($reply['body']) ?></p>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 text-right mr-1">
                        <?= htmlspecialchars($reply['admin_name']) ?>
                        <?php if ($reply['sent_email']): ?>
                            · <span class="text-green-500">✓ Email sent</span>
                        <?php endif; ?>
                        · <?= date('M j, g:i A', strtotime($reply['created_at'])) ?>
                    </p>
                </div>
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold text-sm flex-shrink-0 mt-1">
                    <?= strtoupper(substr($reply['admin_name'], 0, 1)) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Dynamic replies will be appended here by JS -->
        <div id="repliesContainer"></div>
    </div>

    <!-- Reply box -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Reply</h2>
        <textarea
            id="replyBody"
            rows="4"
            placeholder="Type your reply…"
            class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 resize-none"
        ></textarea>

        <!-- Send email toggle -->
        <label class="flex items-center gap-2 mt-3 cursor-pointer select-none">
            <input type="checkbox" id="sendEmailToggle" class="w-4 h-4 accent-pink-500" checked />
            <span class="text-sm text-gray-600">Also send reply via email to <strong><?= htmlspecialchars($message['customer_email']) ?></strong></span>
        </label>

        <div class="flex justify-end mt-4 gap-2">
            <button
                id="sendReplyBtn"
                class="px-5 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Send Reply
            </button>
        </div>
    </div>

</div>

<script>
const CSRF_TOKEN   = '<?= csrf_token() ?>';
const MESSAGE_ID   = <?= $message['id'] ?>;
const ADMIN_NAME   = '<?= addslashes(htmlspecialchars($_SESSION['user_name'] ?? 'Admin')) ?>';

// ─── Send reply ───────────────────────────────────────────────────────────────
document.getElementById('sendReplyBtn').addEventListener('click', async function () {
    const body      = document.getElementById('replyBody').value.trim();
    const sendEmail = document.getElementById('sendEmailToggle').checked;

    if (! body) {
        showToast('Reply cannot be empty.', 'error');
        return;
    }

    this.disabled    = true;
    this.textContent = 'Sending…';

    const res = await postJSON(`/admin/inquiries/${MESSAGE_ID}/reply`, {
        body,
        send_email: sendEmail ? 1 : 0,
    });

    this.disabled = false;
    this.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Send Reply`;

    if (res.success) {
        appendReply(res.data.reply);
        document.getElementById('replyBody').value = '';
        showToast(res.message, 'success');

        // Update status badge if auto-resolved
        updateStatusBadge('resolved');
    } else {
        showToast(res.message, 'error');
    }
});

function appendReply(reply) {
    const container = document.getElementById('repliesContainer');
    const emailBadge = reply.sent_email
        ? '· <span class="text-green-500">✓ Email sent</span>'
        : '';

    container.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3 justify-end" id="reply-${reply.id}">
            <div class="flex-1 max-w-lg">
                <div class="bg-pink-500 text-white rounded-2xl rounded-tr-sm px-4 py-3">
                    <p class="text-sm whitespace-pre-wrap">${reply.body}</p>
                </div>
                <p class="text-xs text-gray-400 mt-1 text-right mr-1">
                    ${reply.admin_name} ${emailBadge} · ${reply.created_at}
                </p>
            </div>
            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold text-sm flex-shrink-0 mt-1">
                ${reply.admin_name.charAt(0).toUpperCase()}
            </div>
        </div>
    `);

    // Scroll to new reply
    container.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'end' });
}

// ─── Status toggle ────────────────────────────────────────────────────────────
document.getElementById('statusToggleBtn').addEventListener('click', async function () {
    const current = this.dataset.current;
    const next    = current === 'open' ? 'resolved' : 'open';

    const res = await postJSON(`/admin/inquiries/${MESSAGE_ID}/status`, { status: next });

    if (res.success) {
        updateStatusBadge(next);
        showToast(res.message, 'success');
    } else {
        showToast(res.message, 'error');
    }
});

function updateStatusBadge(status) {
    const btn   = document.getElementById('statusToggleBtn');
    const dot   = btn.querySelector('.status-dot');
    const label = btn.querySelector('.status-label');

    btn.dataset.current = status;
    label.textContent   = status.charAt(0).toUpperCase() + status.slice(1);

    if (status === 'resolved') {
        dot.className = 'status-dot w-1.5 h-1.5 rounded-full bg-green-500';
        btn.className = btn.className.replace(/bg-\S+|text-\S+|hover:bg-\S+/g, '').trim()
            + ' bg-green-100 text-green-700 hover:bg-green-200';
    } else {
        dot.className = 'status-dot w-1.5 h-1.5 rounded-full bg-yellow-500';
        btn.className = btn.className.replace(/bg-\S+|text-\S+|hover:bg-\S+/g, '').trim()
            + ' bg-yellow-100 text-yellow-700 hover:bg-yellow-200';
    }
}

// ─── Shared fetch helper ──────────────────────────────────────────────────────
async function postJSON(url, data) {
    try {
        const res = await fetch(url, {
            method  : 'POST',
            headers : { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body    : JSON.stringify(data),
        });
        return await res.json();
    } catch {
        return { success: false, message: 'Network error. Please try again.' };
    }
}
</script>