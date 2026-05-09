<?php
// ─────────────────────────────────────────────
//  app/models/PromoCode.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class PromoCode {

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Admin list ────────────────────────────
    public function getAll(int $limit = 50, int $offset = 0): array {
        return $this->db->query(
            'SELECT * FROM promo_codes ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [$limit, $offset]
        );
    }

    public function countAll(): int {
        $row = $this->db->queryOne('SELECT COUNT(*) AS n FROM promo_codes');
        return (int)($row['n'] ?? 0);
    }

    // ── Single lookup ─────────────────────────
    public function findById(int $id): ?array {
        return $this->db->queryOne(
            'SELECT * FROM promo_codes WHERE id = ?',
            [$id]
        ) ?: null;
    }

    public function findByCode(string $code): ?array {
        return $this->db->queryOne(
            'SELECT * FROM promo_codes WHERE code = ?',
            [strtoupper(trim($code))]
        ) ?: null;
    }

    // ── Validate & apply ──────────────────────
    /**
     * Returns ['discount' => float, 'error' => null] on success
     * or     ['discount' => 0,     'error' => 'message'] on failure.
     */
    public function validate(string $code, float $subtotal): array {
        $promo = $this->findByCode($code);

        if (!$promo) {
            return ['discount' => 0, 'error' => 'Invalid promo code.'];
        }

        if (!$promo['is_active']) {
            return ['discount' => 0, 'error' => 'This promo code is inactive.'];
        }

        if ($promo['expires_at'] && strtotime($promo['expires_at']) < time()) {
            return ['discount' => 0, 'error' => 'This promo code has expired.'];
        }

        if ($promo['max_uses'] !== null && $promo['uses'] >= $promo['max_uses']) {
            return ['discount' => 0, 'error' => 'This promo code has reached its usage limit.'];
        }

        if ($subtotal < (float)$promo['min_order']) {
            return [
                'discount' => 0,
                'error'    => 'Minimum order of ₱' . number_format((float)$promo['min_order'], 2) . ' required.',
            ];
        }

        $discount = $promo['type'] === 'percent'
            ? $subtotal * ((float)$promo['value'] / 100)
            : (float)$promo['value'];

        // Discount cannot exceed subtotal
        $discount = min($discount, $subtotal);

        return ['discount' => round($discount, 2), 'error' => null, 'promo' => $promo];
    }

    // ── Increment usage count ─────────────────
    public function incrementUses(int $id): void {
        $this->db->execute(
            'UPDATE promo_codes SET uses = uses + 1 WHERE id = ?',
            [$id]
        );
    }

    // ── CRUD ──────────────────────────────────
    public function create(array $data): int|false {
        $ok = $this->db->execute(
            'INSERT INTO promo_codes (code, type, value, min_order, max_uses, is_active, expires_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                strtoupper(trim($data['code'])),
                $data['type'],
                $data['value'],
                $data['min_order']  ?? 0,
                !empty($data['max_uses']) ? (int)$data['max_uses'] : null,
                isset($data['is_active']) ? (int)$data['is_active'] : 1,
                !empty($data['expires_at']) ? $data['expires_at'] : null,
            ]
        );
        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool {
        return $this->db->execute(
            'UPDATE promo_codes
             SET code = ?, type = ?, value = ?, min_order = ?, max_uses = ?,
                 is_active = ?, expires_at = ?
             WHERE id = ?',
            [
                strtoupper(trim($data['code'])),
                $data['type'],
                $data['value'],
                $data['min_order']  ?? 0,
                !empty($data['max_uses']) ? (int)$data['max_uses'] : null,
                isset($data['is_active']) ? (int)$data['is_active'] : 1,
                !empty($data['expires_at']) ? $data['expires_at'] : null,
                $id,
            ]
        );
    }

    public function delete(int $id): bool {
        return $this->db->execute(
            'DELETE FROM promo_codes WHERE id = ?',
            [$id]
        );
    }
}