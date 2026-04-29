<?php
// ─────────────────────────────────────────────
//  app/models/User.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class User {

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Find by ID ────────────────────────────
    public function findById(int $id): array|false {
        return $this->db->queryOne(
            "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1",
            [$id]
        );
    }

    // ── Find by Email ─────────────────────────
    public function findByEmail(string $email): array|false {
        return $this->db->queryOne(
            "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1",
            [$email]
        );
    }

    // ── Get all users (admin) ─────────────────
    public function getAll(int $limit = 10, int $offset = 0): array {
        return $this->db->query(
            "SELECT id, name, email, role, phone, is_active, created_at
             FROM users
             WHERE deleted_at IS NULL
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public function countAll(): int {
        $row = $this->db->queryOne(
            "SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL"
        );
        return (int) ($row['total'] ?? 0);
    }

    // ── Register new user ─────────────────────
    public function create(array $data): string|false {
        // Check email uniqueness
        if ($this->findByEmail($data['email'])) {
            return false;
        }

        return $this->db->insert(
            "INSERT INTO users (name, email, password, role, phone, address)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                $data['role']    ?? ROLE_CUSTOMER,
                $data['phone']   ?? null,
                $data['address'] ?? null,
            ]
        );
    }

    // ── Verify login credentials ──────────────
    public function verifyCredentials(string $email, string $password): array|false {
        $user = $this->findByEmail($email);

        if (!$user) return false;
        if (!$user['is_active']) return false;
        if (!password_verify($password, $user['password'])) return false;

        // Rehash if needed (future-proof)
        if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $this->updatePassword($user['id'], $password);
        }

        return $user;
    }

    // ── Update ────────────────────────────────
    public function update(int $id, array $data): int {
        return $this->db->execute(
            "UPDATE users SET name = ?, phone = ?, address = ?, updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL",
            [$data['name'], $data['phone'] ?? null, $data['address'] ?? null, $id]
        );
    }

    public function updatePassword(int $id, string $newPassword): int {
        return $this->db->execute(
            "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?",
            [password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]
        );
    }

    public function updateRole(int $id, string $role): int {
        return $this->db->execute(
            "UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?",
            [$role, $id]
        );
    }

    public function toggleActive(int $id): int {
        return $this->db->execute(
            "UPDATE users SET is_active = NOT is_active, updated_at = NOW() WHERE id = ?",
            [$id]
        );
    }

    // ── Soft Delete ───────────────────────────
    public function delete(int $id): int {
        return $this->db->execute(
            "UPDATE users SET deleted_at = NOW() WHERE id = ?",
            [$id]
        );
    }

    // ── Email exists check ────────────────────
    public function emailExists(string $email, ?int $excludeId = null): bool {
        if ($excludeId) {
            $row = $this->db->queryOne(
                "SELECT id FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL LIMIT 1",
                [$email, $excludeId]
            );
        } else {
            $row = $this->db->queryOne(
                "SELECT id FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1",
                [$email]
            );
        }
        return (bool) $row;
    }
}
