<?php
// ─────────────────────────────────────────────
//  app/models/Product.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class Product {

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Get all active products with filters ──
    public function getAll(array $filters = [], int $limit = 12, int $offset = 0): array {
        $where  = ["p.is_active = 1", "p.deleted_at IS NULL"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[]  = "p.category_id = ?";
            $params[] = (int) $filters['category_id'];
        }

        if (!empty($filters['min_price'])) {
            $where[]  = "p.price >= ?";
            $params[] = (float) $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[]  = "p.price <= ?";
            $params[] = (float) $filters['max_price'];
        }

        if (isset($filters['availability'])) {
            if ($filters['availability'] === 'in_stock') {
                $where[] = "p.stock > 0";
            } elseif ($filters['availability'] === 'out_of_stock') {
                $where[] = "p.stock = 0";
            }
        }

        if (!empty($filters['search'])) {
            $where[]  = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $whereSQL = implode(' AND ', $where);
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE {$whereSQL}
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        );
    }

    // ── Count for pagination ──────────────────
    public function countAll(array $filters = []): int {
        $where  = ["p.is_active = 1", "p.deleted_at IS NULL"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[]  = "p.category_id = ?";
            $params[] = (int) $filters['category_id'];
        }

        if (!empty($filters['min_price'])) {
            $where[]  = "p.price >= ?";
            $params[] = (float) $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[]  = "p.price <= ?";
            $params[] = (float) $filters['max_price'];
        }

        if (isset($filters['availability'])) {
            if ($filters['availability'] === 'in_stock') {
                $where[] = "p.stock > 0";
            } elseif ($filters['availability'] === 'out_of_stock') {
                $where[] = "p.stock = 0";
            }
        }

        if (!empty($filters['search'])) {
            $where[]  = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $whereSQL = implode(' AND ', $where);

        $row = $this->db->queryOne(
            "SELECT COUNT(*) as total
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE {$whereSQL}",
            $params
        );

        return (int) ($row['total'] ?? 0);
    }

    // ── Find by ID ────────────────────────────
    public function findById(int $id): array|false {
        return $this->db->queryOne(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = ? AND p.is_active = 1 AND p.deleted_at IS NULL
             LIMIT 1",
            [$id]
        );
    }

    // ── Find by slug ──────────────────────────
    public function findBySlug(string $slug): array|false {
        return $this->db->queryOne(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.slug = ? AND p.is_active = 1 AND p.deleted_at IS NULL
             LIMIT 1",
            [$slug]
        );
    }

    // ── Get all categories ────────────────────
    public function getCategories(): array {
        return $this->db->query(
            "SELECT * FROM categories ORDER BY name ASC"
        );
    }

    // ── Get price range ───────────────────────
    public function getPriceRange(): array {
        return $this->db->queryOne(
            "SELECT MIN(price) as min_price, MAX(price) as max_price
             FROM products
             WHERE is_active = 1 AND deleted_at IS NULL"
        ) ?: ['min_price' => 0, 'max_price' => 9999];
    }

    // ── Admin: get all including inactive ─────
    public function adminGetAll(int $limit = 20, int $offset = 0): array {
        return $this->db->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.deleted_at IS NULL
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    // ── Create ────────────────────────────────
    public function create(array $data): string|false {
        return $this->db->insert(
            "INSERT INTO products (category_id, name, slug, description, price, stock, low_stock_alert, image, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['category_id'],
                $data['name'],
                $data['slug'],
                $data['description']    ?? null,
                $data['price'],
                $data['stock']          ?? 0,
                $data['low_stock_alert'] ?? 5,
                $data['image']          ?? null,
                $data['is_active']      ?? 1,
            ]
        );
    }

    // ── Update ────────────────────────────────
    public function update(int $id, array $data): int {
        return $this->db->execute(
            "UPDATE products
             SET category_id = ?, name = ?, slug = ?, description = ?, price = ?,
                 stock = ?, low_stock_alert = ?, image = ?, is_active = ?, updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL",
            [
                $data['category_id'],
                $data['name'],
                $data['slug'],
                $data['description']     ?? null,
                $data['price'],
                $data['stock']           ?? 0,
                $data['low_stock_alert'] ?? 5,
                $data['image']           ?? null,
                $data['is_active']       ?? 1,
                $id,
            ]
        );
    }

    // ── Soft delete ───────────────────────────
    public function delete(int $id): int {
        return $this->db->execute(
            "UPDATE products SET deleted_at = NOW() WHERE id = ?",
            [$id]
        );
    }

    // ── Update stock ──────────────────────────
    public function updateStock(int $id, int $quantity): int {
        return $this->db->execute(
            "UPDATE products SET stock = ?, updated_at = NOW() WHERE id = ?",
            [$quantity, $id]
        );
    }
}