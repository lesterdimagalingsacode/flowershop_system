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

    // ── Find by ID (static) ───────────────────
    public static function find(int $id): ?array {
        $db  = Database::getInstance();
        $row = $db->queryOne(
            "SELECT * FROM users WHERE id = :id AND deleted_at IS NULL",
            [':id' => $id]
        );
        return $row ?: null;
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

    // ── Get filtered users (admin, paginated) ─
    public static function getFiltered(array $filters, int $page = 1, int $perPage = 15): array
    {
        $db     = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        [$where, $params] = self::buildWhereClause($filters);

        $sql = "
            SELECT id, first_name, middle_name, last_name, email, role, is_active, created_at, deleted_at
            FROM users
            {$where}
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $params[':limit']  = $perPage;
        $params[':offset'] = $offset;

        $users = $db->query($sql, $params);

        // Append computed full name so views can use $user['name']
        return array_map(function(array $u): array {
            $parts    = array_filter([$u['first_name'] ?? '', $u['middle_name'] ?? '', $u['last_name'] ?? '']);
            $u['name'] = implode(' ', $parts);
            return $u;
        }, $users);
    }

    // ── Count filtered users ──────────────────
    public static function countFiltered(array $filters): int
    {
        $db = Database::getInstance();

        [$where, $params] = self::buildWhereClause($filters);

        $sql = "SELECT COUNT(*) as total FROM users {$where}";

        $row = $db->queryOne($sql, $params);
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

    // ── Update single field (static, whitelisted) ─
    public static function updateField(int $id, string $field, mixed $value): bool
    {
        $allowed = ['role', 'is_active'];
        if (! in_array($field, $allowed, true)) {
            return false;
        }

        $db = Database::getInstance();
        return $db->execute(
            "UPDATE users SET {$field} = :value, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL",
            [':value' => $value, ':id' => $id]
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

    // ── Soft Delete (static) ──────────────────
    public static function softDelete(int $id): bool
    {
        $db = Database::getInstance();
        return $db->execute(
            "UPDATE users SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL",
            [':id' => $id]
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

    // ── PRIVATE HELPER: buildWhereClause ──────
    private static function buildWhereClause(array $filters): array
    {
        $conditions = ['deleted_at IS NULL'];
        $params     = [];

        if (! empty($filters['search'])) {
            // Search across actual columns — no virtual 'name' column
            $conditions[] = "(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (! empty($filters['role'])) {
            $conditions[] = "role = :role";
            $params[':role'] = $filters['role'];
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $conditions[] = "is_active = 1";
            } elseif ($filters['status'] === 'inactive') {
                $conditions[] = "is_active = 0";
            }
        }

        $where = 'WHERE ' . implode(' AND ', $conditions);
        return [$where, $params];
    }

    // ── Email Verification ────────────────────────
    public function setVerificationToken(int $id, string $token): void {
        $this->db->execute(
            "UPDATE users SET verification_token = ?, updated_at = NOW() WHERE id = ?",
            [$token, $id]
        );
    }

    public function findByVerificationToken(string $token): array|false {
        return $this->db->queryOne(
            "SELECT * FROM users WHERE verification_token = ? AND deleted_at IS NULL LIMIT 1",
            [$token]
        );
    }

    public function markEmailVerified(int $id): void {
        $this->db->execute(
            "UPDATE users SET email_verified_at = NOW(), verification_token = NULL, updated_at = NOW() WHERE id = ?",
            [$id]
        );
    }

    public function isEmailVerified(int $id): bool {
        $row = $this->db->queryOne(
            "SELECT email_verified_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1",
            [$id]
        );
        return !empty($row['email_verified_at']);
    }
}