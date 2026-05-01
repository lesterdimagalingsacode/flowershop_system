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
        $user = $this->db->queryOne(
            "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1",
            [$id]
        );
        return $user ? $this->appendFullName($user) : false;
    }

    // ── Find by Email ─────────────────────────
    public function findByEmail(string $email): array|false {
        $user = $this->db->queryOne(
            "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1",
            [$email]
        );
        return $user ? $this->appendFullName($user) : false;
    }

    // ── Get all users (admin) ─────────────────
    public function getAll(int $limit = 10, int $offset = 0): array {
        $users = $this->db->query(
            "SELECT id, first_name, middle_name, last_name, email, role, phone, is_active, created_at
             FROM users
             WHERE deleted_at IS NULL
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        return array_map([$this, 'appendFullName'], $users);
    }

    public function countAll(): int {
        $row = $this->db->queryOne(
            "SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL"
        );
        return (int) ($row['total'] ?? 0);
    }

    // ── Register new user ─────────────────────
    public function create(array $data): string|false {
        if ($this->findByEmail($data['email'])) {
            return false;
        }

        return $this->db->insert(
            "INSERT INTO users (first_name, middle_name, last_name, email, password, role, phone, address)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['first_name'],
                $data['middle_name'] ?? null,
                $data['last_name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                $data['role']        ?? ROLE_CUSTOMER,
                $data['phone']       ?? null,
                $data['address']     ?? null,
            ]
        );
    }

    // ── Verify login credentials ──────────────
    public function verifyCredentials(string $email, string $password): array|false {
        $user = $this->findByEmail($email);

        if (!$user)               return false;
        if (!$user['is_active'])  return false;
        if (!password_verify($password, $user['password'])) return false;

        if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $this->updatePassword($user['id'], $password);
        }

        return $user;
    }

    // ── Update ────────────────────────────────
    public function update(int $id, array $data): int {
        return $this->db->execute(
            "UPDATE users 
             SET first_name = ?, middle_name = ?, last_name = ?, phone = ?, address = ?, updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL",
            [
                $data['first_name'],
                $data['middle_name'] ?? null,
                $data['last_name'],
                $data['phone']       ?? null,
                $data['address']     ?? null,
                $id,
            ]
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

    // ── Helper: build full name ───────────────
    private function appendFullName(array $user): array {
        $parts = array_filter([
            $user['first_name']  ?? '',
            $user['middle_name'] ?? '',
            $user['last_name']   ?? '',
        ]);
        $user['name'] = implode(' ', $parts);
        return $user;
    }
}