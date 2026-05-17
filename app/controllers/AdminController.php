<?php
// ─────────────────────────────────────────────
//  app/controllers/AdminController.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class AdminController extends Controller {

    // ── Users list (paginated, filtered) ─────
    public function users(): void
    {
        $this->requireStaff();

        $search  = trim($this->get('search', ''));
        $role    = $this->get('role',   '');   // '', 'customer', 'staff', 'admin'
        $status  = $this->get('status', '');   // '', 'active', 'inactive'
        $page    = max(1, (int) $this->get('page', 1));
        $perPage = 15;

        $filters = compact('search', 'role', 'status');

        $total      = User::countFiltered($filters);
        $users      = User::getFiltered($filters, $page, $perPage);
        $totalPages = (int) ceil($total / $perPage);

        $this->view('admin/users', [
            'users'      => $users,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => $totalPages,
            'search'     => $search,
            'role'       => $role,
            'status'     => $status,
        ], 'admin');
    }

    // ── Update role ───────────────────────────
    public function updateRole(): void
    {
        $this->requireStaff();
        CSRFMiddleware::verify();

        $userId  = (int) $this->post('user_id');
        $newRole = $this->post('role', '');

        $allowedRoles = ['customer', 'staff', 'admin'];

        if (! $userId || ! in_array($newRole, $allowedRoles, true)) {
            $this->jsonError('Invalid request.');
            return;
        }

        if ($userId === (int) $_SESSION['user_id']) {
            $this->jsonError('You cannot change your own role.');
            return;
        }

        $user = User::find($userId);
        if (! $user) {
            $this->jsonError('User not found.');
            return;
        }

        $updated = User::updateField($userId, 'role', $newRole);

        if ($updated) {
            // ── NEW ───────────────────────────────────
            Logger::audit('user.role_changed', [
                'model'    => 'User',
                'model_id' => $userId,
                'old'      => ['role' => $user['role']],
                'new'      => ['role' => $newRole],
            ]);
            // ─────────────────────────────────────────

            $this->jsonSuccess(['role' => $newRole], 'Role updated successfully.');
        } else {
            $this->jsonError('Failed to update role.');
        }
    }

    // ── Toggle active/inactive ────────────────
    public function toggleActive(): void
    {
        $this->requireStaff();
        CSRFMiddleware::verify();

        $userId = (int) $this->post('user_id');

        if (! $userId) {
            $this->jsonError('Invalid request.');
            return;
        }

        if ($userId === (int) $_SESSION['user_id']) {
            $this->jsonError('You cannot deactivate your own account.');
            return;
        }

        $user = User::find($userId);
        if (! $user) {
            $this->jsonError('User not found.');
            return;
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $updated   = User::updateField($userId, 'is_active', $newStatus);

        if ($updated) {
            $label = $newStatus ? 'activated' : 'deactivated';

            // ── NEW ───────────────────────────────────
            Logger::audit('user.toggled', [
                'model'    => 'User',
                'model_id' => $userId,
                'old'      => ['is_active' => $user['is_active']],
                'new'      => ['is_active' => $newStatus],
            ]);
            // ─────────────────────────────────────────

            $this->jsonSuccess(['is_active' => $newStatus], "User {$label} successfully.");
        } else {
            $this->jsonError('Failed to update status.');
        }
    }

    // ── Soft delete user ──────────────────────
    public function destroy(): void
    {
        $this->requireStaff();
        CSRFMiddleware::verify();

        $userId = (int) $this->post('user_id');

        if (! $userId) {
            $this->jsonError('Invalid request.');
            return;
        }

        if ($userId === (int) $_SESSION['user_id']) {
            $this->jsonError('You cannot delete your own account.');
            return;
        }

        $user = User::find($userId);
        if (! $user) {
            $this->jsonError('User not found.');
            return;
        }

        $deleted = User::softDelete($userId);

        if ($deleted) {
            // ── NEW ───────────────────────────────────
            Logger::audit('user.deleted', [
                'model'    => 'User',
                'model_id' => $userId,
                'old'      => ['email' => $user['email'], 'role' => $user['role']],
            ]);
            // ─────────────────────────────────────────

            $this->jsonSuccess([], 'User deleted successfully.');
        } else {
            $this->jsonError('Failed to delete user.');
        }
    }
}