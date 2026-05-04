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
}