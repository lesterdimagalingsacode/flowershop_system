<?php
// ─────────────────────────────────────────────
//  app/models/Order.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class Order {

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Create a new order ────────────────────
    public function create(array $data): string|false {
        $orderNumber = $this->generateOrderNumber();

        return $this->db->insert(
            "INSERT INTO orders (user_id, order_number, status, subtotal, delivery_fee, discount_amount, total_amount, delivery_address, notes, promo_code, payment_method)
             VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['user_id'],
                $orderNumber,
                $data['subtotal'],
                $data['delivery_fee']    ?? 0.00,
                $data['discount_amount'] ?? 0.00,
                $data['total_amount'],
                $data['delivery_address'],
                $data['notes']           ?? null,
                $data['promo_code']      ?? null,
                $data['payment_method']  ?? 'cod',
            ]
        );
    }

    // ── Find by ID ────────────────────────────
    public function findById(int $id): array|false {
        return $this->db->queryOne(
            "SELECT o.*, u.first_name, u.last_name, u.email
             FROM orders o
             JOIN users u ON o.user_id = u.id
             WHERE o.id = ? AND o.deleted_at IS NULL
             LIMIT 1",
            [$id]
        );
    }

    // ── Find by order number ──────────────────
    public function findByOrderNumber(string $orderNumber): array|false {
        return $this->db->queryOne(
            "SELECT o.*, u.first_name, u.last_name, u.email
             FROM orders o
             JOIN users u ON o.user_id = u.id
             WHERE o.order_number = ? AND o.deleted_at IS NULL
             LIMIT 1",
            [$orderNumber]
        );
    }

    // ── Get orders by user ────────────────────
    public function getByUser(int $userId, int $limit = 10, int $offset = 0): array {
        return $this->db->query(
            "SELECT * FROM orders
             WHERE user_id = ? AND deleted_at IS NULL
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }

    public function countByUser(int $userId): int {
        $row = $this->db->queryOne(
            "SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND deleted_at IS NULL",
            [$userId]
        );
        return (int)($row['total'] ?? 0);
    }

    // ── Get all orders (admin) ────────────────
    public function getAll(int $limit = 20, int $offset = 0, string $status = '', string $dateFrom = '', string $dateTo = ''): array {
        $conditions = ["o.deleted_at IS NULL"];
        $params     = [];

        if ($status) {
            $conditions[] = "o.status = ?";
            $params[]     = $status;
        }
        if ($dateFrom) {
            $conditions[] = "DATE(o.created_at) >= ?";
            $params[]     = $dateFrom;
        }
        if ($dateTo) {
            $conditions[] = "DATE(o.created_at) <= ?";
            $params[]     = $dateTo;
        }

        $where    = "WHERE " . implode(" AND ", $conditions);
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->query(
            "SELECT o.*, u.first_name, u.last_name
             FROM orders o
             JOIN users u ON o.user_id = u.id
             {$where}
             ORDER BY o.created_at ASC
             LIMIT ? OFFSET ?",
            $params
        );
    }

    public function countAll(string $status = '', string $dateFrom = '', string $dateTo = ''): int {
        $conditions = ["deleted_at IS NULL"];
        $params     = [];

        if ($status) {
            $conditions[] = "status = ?";
            $params[]     = $status;
        }
        if ($dateFrom) {
            $conditions[] = "DATE(created_at) >= ?";
            $params[]     = $dateFrom;
        }
        if ($dateTo) {
            $conditions[] = "DATE(created_at) <= ?";
            $params[]     = $dateTo;
        }

        $where = "WHERE " . implode(" AND ", $conditions);
        $row   = $this->db->queryOne(
            "SELECT COUNT(*) as total FROM orders {$where}",
            $params
        );
        return (int)($row['total'] ?? 0);
    }

    // ── Update status ─────────────────────────
    public function updateStatus(int $id, string $status, int $changedBy, string $notes = ''): bool {
        $order = $this->findById($id);
        if (!$order) return false;

        $this->db->execute(
            "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?",
            [$status, $id]
        );

        $this->db->execute(
            "INSERT INTO order_status_history (order_id, changed_by, from_status, to_status, notes)
             VALUES (?, ?, ?, ?, ?)",
            [$id, $changedBy, $order['status'], $status, $notes ?: null]
        );

        return true;
    }

    // ── Cancel order ──────────────────────────
    public function cancel(int $id, int $userId): bool {
        $order = $this->findById($id);
        if (!$order) return false;
        if ((int)$order['user_id'] !== $userId) return false;
        if (!in_array($order['status'], ['pending', 'confirmed'])) return false;

        return $this->updateStatus($id, 'cancelled', $userId, 'Cancelled by customer');
    }

    // ── Get status history ────────────────────
    public function getStatusHistory(int $orderId): array {
        return $this->db->query(
            "SELECT h.*, u.first_name, u.last_name
             FROM order_status_history h
             LEFT JOIN users u ON h.changed_by = u.id
             WHERE h.order_id = ?
             ORDER BY h.created_at ASC",
            [$orderId]
        );
    }

    // ── Get payment for order ─────────────────
    public function getPayment(int $orderId): array|false {
        return $this->db->queryOne(
            "SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1",
            [$orderId]
        );
    }

    // ══════════════════════════════════════════
    //  DASHBOARD STATS
    // ══════════════════════════════════════════

    public function getTotalRevenue(): float {
        $row = $this->db->queryOne(
            "SELECT SUM(total_amount) as revenue
            FROM orders
            WHERE status = 'delivered' AND deleted_at IS NULL"
        );
        return (float)($row['revenue'] ?? 0);
    }

    public function getTodayOrders(): int {
        $row = $this->db->queryOne(
            "SELECT COUNT(*) as total FROM orders
             WHERE DATE(created_at) = CURDATE() AND deleted_at IS NULL"
        );
        return (int)($row['total'] ?? 0);
    }

    public function getRevenueInRange(string $from, string $to): float {
        $row = $this->db->queryOne(
            "SELECT SUM(total_amount) as revenue
            FROM orders
            WHERE status = 'delivered' AND deleted_at IS NULL
            AND DATE(created_at) BETWEEN ? AND ?",
            [$from, $to]
        );
        return (float)($row['revenue'] ?? 0);
    }

    public function countInRange(string $from, string $to): int {
        $row = $this->db->queryOne(
            "SELECT COUNT(*) as total FROM orders
             WHERE deleted_at IS NULL
               AND DATE(created_at) BETWEEN ? AND ?",
            [$from, $to]
        );
        return (int)($row['total'] ?? 0);
    }

    public function countNewCustomers(string $from, string $to): int {
        $row = $this->db->queryOne(
            "SELECT COUNT(DISTINCT user_id) as total FROM orders
             WHERE deleted_at IS NULL
               AND DATE(created_at) BETWEEN ? AND ?",
            [$from, $to]
        );
        return (int)($row['total'] ?? 0);
    }

    public function getSalesByDay(string $from, string $to): array {
        return $this->db->query(
            "SELECT DATE(created_at) as date, COUNT(*) as orders
             FROM orders
             WHERE deleted_at IS NULL
               AND DATE(created_at) BETWEEN ? AND ?
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [$from, $to]
        );
    }

    public function getRevenueByDay(string $from, string $to): array {
        return $this->db->query(
            "SELECT DATE(created_at) as date, SUM(total_amount) as revenue
            FROM orders
            WHERE status = 'delivered' AND deleted_at IS NULL
            AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY date ASC",
            [$from, $to]
        );
    }

    public function getCountByStatus(): array {
        return $this->db->query(
            "SELECT status, COUNT(*) as total
             FROM orders
             WHERE deleted_at IS NULL
             GROUP BY status
             ORDER BY total DESC"
        );
    }

    private function generateOrderNumber(): string {
        return 'PS-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Ymd');
    }
}