<?php
// app/views/admin/users.php
// Layout: admin

$currentUserId = Session::userId();
?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Users</h1>
            <p class="text-sm text-gray-500 mt-1">
                <?= number_format($total) ?> user<?= $total !== 1 ? 's' : '' ?> found
            </p>
        </div>
    </div>

    <!-- Filters Bar -->
    <form method="GET" action="/admin/users" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">

            <!-- Search -->
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search by name or email…"
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                />
            </div>

            <!-- Role Filter -->
            <select name="role" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
                <option value="">All Roles</option>
                <option value="customer"  <?= $role === 'customer' ? 'selected' : '' ?>>Customer</option>
                <option value="staff"     <?= $role === 'staff'    ? 'selected' : '' ?>>Staff</option>
                <option value="admin"     <?= $role === 'admin'    ? 'selected' : '' ?>>Admin</option>
            </select>

            <!-- Status Filter -->
            <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
                <option value="">All Status</option>
                <option value="active"   <?= $status === 'active'   ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition">
                Filter
            </button>

            <?php if ($search || $role || $status): ?>
                <a href="/admin/users" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg transition flex items-center">
                    Clear
                </a>
            <?php endif; ?>

        </div>
    </form>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 0 0-5-3.87M9 20H4v-2a4 4 0 0 1 5-3.87M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/>
                                </svg>
                                No users found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50 transition user-row" data-id="<?= $user['id'] ?>">

                                <!-- User info -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-semibold text-sm flex-shrink-0">
                                            <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900"><?= htmlspecialchars($user['name'] ?? '') ?></div>
                                            <div class="text-gray-400 text-xs"><?= htmlspecialchars($user['email']) ?></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role badge + changer -->
                                <td class="px-6 py-4">
                                    <?php if ((int)$user['id'] === $currentUserId): ?>
                                        <?= roleBadge($user['role']) ?>
                                        <span class="text-xs text-gray-400 ml-1">(you)</span>
                                    <?php else: ?>
                                        <select
                                            class="role-select text-xs border border-gray-200 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-pink-400"
                                            data-user-id="<?= $user['id'] ?>"
                                            data-original="<?= $user['role'] ?>"
                                        >
                                            <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                            <option value="staff"    <?= $user['role'] === 'staff'    ? 'selected' : '' ?>>Staff</option>
                                            <option value="admin"    <?= $user['role'] === 'admin'    ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    <?php endif; ?>
                                </td>

                                <!-- Active status toggle -->
                                <td class="px-6 py-4">
                                    <?php if ((int)$user['id'] === $currentUserId): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                        </span>
                                    <?php else: ?>
                                        <button
                                            class="toggle-active-btn inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium transition
                                                <?= $user['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' ?>"
                                            data-user-id="<?= $user['id'] ?>"
                                        >
                                            <span class="status-dot w-1.5 h-1.5 rounded-full <?= $user['is_active'] ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
                                            <span class="status-label"><?= $user['is_active'] ? 'Active' : 'Inactive' ?></span>
                                        </button>
                                    <?php endif; ?>
                                </td>

                                <!-- Joined date -->
                                <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                    <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <?php if ((int)$user['id'] !== $currentUserId): ?>
                                        <button
                                            class="delete-user-btn text-xs text-red-400 hover:text-red-600 font-medium transition"
                                            data-user-id="<?= $user['id'] ?>"
                                            data-user-name="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                        >
                                            Delete
                                        </button>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Showing <?= (($page - 1) * $perPage) + 1 ?>–<?= min($page * $perPage, $total) ?> of <?= number_format($total) ?>
                </p>
                <div class="flex gap-1">
                    <?php
                    $queryBase = http_build_query(array_filter([
                        'search' => $search,
                        'role'   => $role,
                        'status' => $status,
                    ]));
                    $queryBase = $queryBase ? '&' . $queryBase : '';
                    ?>
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 . $queryBase ?>" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition">← Prev</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?= $i . $queryBase ?>"
                           class="px-3 py-1 text-sm border rounded-md transition <?= $i === $page ? 'bg-pink-500 text-white border-pink-500' : 'border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 . $queryBase ?>" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition">Next →</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div><!-- /table card -->
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Delete User</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">
            Are you sure you want to delete <strong id="deleteUserName"></strong>?
        </p>
        <div class="flex gap-3">
            <button id="cancelDelete" class="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button id="confirmDelete" class="flex-1 px-4 py-2 text-sm bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition">Delete</button>
        </div>
    </div>
</div>

<?php
function roleBadge(string $role): string {
    $map = [
        'admin'    => 'bg-purple-100 text-purple-700',
        'staff'    => 'bg-blue-100 text-blue-700',
        'customer' => 'bg-gray-100 text-gray-600',
    ];
    $class = $map[$role] ?? 'bg-gray-100 text-gray-600';
    return "<span class=\"inline-block px-2 py-0.5 rounded-full text-xs font-medium {$class}\">" . ucfirst(htmlspecialchars($role)) . "</span>";
}
?>

<script>
const CSRF_TOKEN = '<?= csrf_token() ?>';

document.querySelectorAll('.role-select').forEach(select => {
    select.addEventListener('change', async function () {
        const userId   = this.dataset.userId;
        const newRole  = this.value;
        const original = this.dataset.original;
        const res = await postJSON('/admin/users/update-role', { user_id: userId, role: newRole });
        if (res.success) {
            this.dataset.original = newRole;
            showToast(res.message, 'success');
        } else {
            this.value = original;
            showToast(res.message, 'error');
        }
    });
});

document.querySelectorAll('.toggle-active-btn').forEach(btn => {
    btn.addEventListener('click', async function () {
        const res = await postJSON('/admin/users/toggle-active', { user_id: this.dataset.userId });
        if (res.success) {
            const isActive = res.data.is_active === 1;
            const dot   = this.querySelector('.status-dot');
            const label = this.querySelector('.status-label');
            label.textContent = isActive ? 'Active' : 'Inactive';
            dot.className     = `status-dot w-1.5 h-1.5 rounded-full ${isActive ? 'bg-green-500' : 'bg-gray-400'}`;
            this.className    = this.className.replace(/bg-\w+-100|text-\w+-\d+|hover:bg-\w+-200/g, '').trim()
                + (isActive ? ' bg-green-100 text-green-700 hover:bg-green-200' : ' bg-gray-100 text-gray-500 hover:bg-gray-200');
            showToast(res.message, 'success');
        } else {
            showToast(res.message, 'error');
        }
    });
});

let pendingDeleteId = null;
const modal = document.getElementById('deleteModal');

document.querySelectorAll('.delete-user-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        pendingDeleteId = this.dataset.userId;
        document.getElementById('deleteUserName').textContent = this.dataset.userName;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
});

document.getElementById('cancelDelete').addEventListener('click', closeModal);
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

document.getElementById('confirmDelete').addEventListener('click', async function () {
    if (!pendingDeleteId) return;
    const res = await postJSON('/admin/users/delete', { user_id: pendingDeleteId });
    if (res.success) {
        const row = document.querySelector(`.user-row[data-id="${pendingDeleteId}"]`);
        if (row) row.remove();
        showToast(res.message, 'success');
    } else {
        showToast(res.message, 'error');
    }
    closeModal();
});

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    pendingDeleteId = null;
}

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