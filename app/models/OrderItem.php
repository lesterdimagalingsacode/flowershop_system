<?php
// ─────────────────────────────────────────────
//  app/models/OrderItem.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class OrderItem {

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Create order items from cart ──────────
    public function createMany(int $orderId, array $cartItems): bool {
        foreach ($cartItems as $item) {
            $result = $this->db->execute(
                "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $item['price'] * $item['quantity'],
                ]
            );
            if (!$result) return false;
        }
        return true;
    }

    // ── Get items by order ────────────────────
    public function getByOrder(int $orderId): array {
        return $this->db->query(
            "SELECT oi.*, p.name, p.slug, p.image
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    // ── Best sellers (by quantity sold) ───────
    public function getBestSellers(string $from, string $to, int $limit = 8): array {
        return $this->db->query(
            "SELECT p.name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as total_revenue
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             JOIN products p ON oi.product_id = p.id
             WHERE o.status NOT IN ('cancelled')
               AND o.deleted_at IS NULL
               AND DATE(o.created_at) BETWEEN ? AND ?
             GROUP BY oi.product_id, p.name
             ORDER BY total_sold DESC
             LIMIT ?",
            [$from, $to, $limit]
        );
    }
}