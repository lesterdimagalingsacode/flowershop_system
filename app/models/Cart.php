<?php
declare(strict_types=1);

class Cart {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getPdo();
    }

    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT c.product_id, c.quantity,
                   p.name, p.slug, p.price, p.image, p.stock
            FROM carts c
            JOIN products p ON p.id = c.product_id
            WHERE c.user_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Reformat to match existing cart structure
        $cart = [];
        foreach ($rows as $row) {
            $cart[$row['product_id']] = [
                'product_id' => $row['product_id'],
                'name'       => $row['name'],
                'slug'       => $row['slug'],
                'price'      => (float) $row['price'],
                'quantity'   => (int) $row['quantity'],
                'image'      => $row['image'],
                'stock'      => (int) $row['stock'],
            ];
        }
        return $cart;
    }

    public function addOrUpdate(int $userId, int $productId, int $quantity): bool {
        $stmt = $this->db->prepare("
            INSERT INTO carts (user_id, product_id, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
        ");
        return $stmt->execute([$userId, $productId, $quantity]);
    }

    public function update(int $userId, int $productId, int $quantity): bool {
        if ($quantity <= 0) {
            return $this->remove($userId, $productId);
        }
        $stmt = $this->db->prepare("
            UPDATE carts SET quantity = ? 
            WHERE user_id = ? AND product_id = ?
        ");
        return $stmt->execute([$quantity, $userId, $productId]);
    }

    public function remove(int $userId, int $productId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM carts WHERE user_id = ? AND product_id = ?
        ");
        return $stmt->execute([$userId, $productId]);
    }

    public function clear(int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM carts WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public function count(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(quantity), 0) FROM carts WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}