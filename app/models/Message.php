<?php

// app/models/Message.php

class Message
{
    // ─── Create ──────────────────────────────────────────────────────────────

    public static function create(array $data): int|false
    {
        $db = Database::getInstance();

        $ok = $db->execute(
            "INSERT INTO messages
                (user_id, customer_name, customer_email, order_id, message, status, is_read)
             VALUES
                (:user_id, :name, :email, :order_id, :message, 'open', 0)",
            [
                ':user_id'  => $data['user_id']  ?? null,
                ':name'     => $data['name'],
                ':email'    => $data['email'],
                ':order_id' => $data['order_id'] ?? null,
                ':message'  => $data['message'],
            ]
        );

        return $ok ? $db->lastInsertId() : false;
    }

    // ─── List (admin) ─────────────────────────────────────────────────────────

    public static function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $db     = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        [$where, $params] = self::buildWhere($filters);

        $sql = "
            SELECT
                m.*,
                o.id AS order_ref,
                (SELECT COUNT(*) FROM message_replies r WHERE r.message_id = m.id) AS reply_count
            FROM messages m
            LEFT JOIN orders o ON o.id = m.order_id
            {$where}
            ORDER BY m.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $params[':limit']  = $perPage;
        $params[':offset'] = $offset;

        return $db->query($sql, $params);
    }

    public static function countAll(array $filters = []): int
    {
        $db = Database::getInstance();
        [$where, $params] = self::buildWhere($filters);
        $row = $db->queryOne("SELECT COUNT(*) AS total FROM messages m {$where}", $params);
        return (int) ($row['total'] ?? 0);
    }

    // ─── Single message + replies ─────────────────────────────────────────────

    public static function find(int $id): ?array
    {
        $db  = Database::getInstance();
        $row = $db->queryOne(
            "SELECT m.*, o.id AS order_ref
             FROM messages m
             LEFT JOIN orders o ON o.id = m.order_id
             WHERE m.id = :id",
            [':id' => $id]
        );
        return $row ?: null;
    }

    public static function getReplies(int $messageId): array
    {
        $db = Database::getInstance();
        return $db->query(
            "SELECT r.*, u.name AS admin_name
             FROM message_replies r
             JOIN users u ON u.id = r.admin_id
             WHERE r.message_id = :mid
             ORDER BY r.created_at ASC",
            [':mid' => $messageId]
        );
    }

    // ─── Mark as read ─────────────────────────────────────────────────────────

    public static function markRead(int $id): void
    {
        $db = Database::getInstance();
        $db->execute(
            "UPDATE messages SET is_read = 1 WHERE id = :id",
            [':id' => $id]
        );
    }

    // ─── Update status ────────────────────────────────────────────────────────

    public static function setStatus(int $id, string $status): bool
    {
        if (! in_array($status, ['open', 'resolved'], true)) return false;

        $db = Database::getInstance();
        return $db->execute(
            "UPDATE messages SET status = :status, updated_at = NOW() WHERE id = :id",
            [':status' => $status, ':id' => $id]
        );
    }

    // ─── Add reply ────────────────────────────────────────────────────────────

    public static function addReply(int $messageId, int $adminId, string $body, bool $sentEmail = false): int|false
    {
        $db = Database::getInstance();
        $ok = $db->execute(
            "INSERT INTO message_replies (message_id, admin_id, body, sent_email)
             VALUES (:mid, :aid, :body, :email)",
            [
                ':mid'   => $messageId,
                ':aid'   => $adminId,
                ':body'  => $body,
                ':email' => $sentEmail ? 1 : 0,
            ]
        );
        return $ok ? $db->lastInsertId() : false;
    }

    // ─── Unread count (for nav badge) ─────────────────────────────────────────

    public static function unreadCount(): int
    {
        $db  = Database::getInstance();
        $row = $db->queryOne("SELECT COUNT(*) AS total FROM messages WHERE is_read = 0");
        return (int) ($row['total'] ?? 0);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private static function buildWhere(array $filters): array
    {
        $conditions = [];
        $params     = [];

        if (! empty($filters['status'])) {
            $conditions[] = "m.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (isset($filters['is_read']) && $filters['is_read'] !== '') {
            $conditions[] = "m.is_read = :is_read";
            $params[':is_read'] = (int) $filters['is_read'];
        }

        if (! empty($filters['search'])) {
            $conditions[] = "(m.customer_name LIKE :search OR m.customer_email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }
}