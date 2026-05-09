<?php
// ─────────────────────────────────────────────
//  core/PusherService.php — Pusher event broadcaster
// ─────────────────────────────────────────────

declare(strict_types=1);

class PusherService
{
    private static ?\Pusher\Pusher $instance = null;

    private static function client(): ?\Pusher\Pusher
    {
        if (self::$instance) return self::$instance;

        if (
            !defined('PUSHER_APP_ID')     || !PUSHER_APP_ID     ||
            !defined('PUSHER_APP_KEY')    || !PUSHER_APP_KEY    ||
            !defined('PUSHER_APP_SECRET') || !PUSHER_APP_SECRET
        ) {
            return null;
        }

        self::$instance = new \Pusher\Pusher(
            PUSHER_APP_KEY,
            PUSHER_APP_SECRET,
            PUSHER_APP_ID,
            [
                'cluster' => defined('PUSHER_APP_CLUSTER') ? PUSHER_APP_CLUSTER : 'ap1',
                'useTLS'  => true,
            ]
        );

        return self::$instance;
    }

    // ── Generic trigger ───────────────────────
    public static function trigger(string $channel, string $event, array $data): void
    {
        try {
            $pusher = self::client();
            if (!$pusher) return;
            $pusher->trigger($channel, $event, $data);
        } catch (\Throwable $e) {
            error_log('[Pusher] Failed to trigger: ' . $e->getMessage());
        }
    }

    // ── Cart updated (per user) ───────────────
    // Fired after any cart add/update/remove
    public static function cartUpdated(int $userId, int $cartCount): void
    {
        self::trigger('private-cart.' . $userId, 'cart-updated', [
            'cart_count' => $cartCount,
        ]);
    }

    // ── New order placed ──────────────────────
    // Fired after customer successfully checks out
    public static function newOrder(array $order): void
    {
        self::trigger('private-admin', 'new-order', [
            'order_id'     => $order['id'],
            'order_number' => $order['order_number'],
            'total_amount' => $order['total_amount'],
            'customer'     => $order['customer_name'] ?? 'Customer',
        ]);
    }

    // ── Order status changed ──────────────────
    // Fired when admin updates order status
    public static function orderStatusChanged(array $order, string $newStatus): void
    {
        // Notify the customer
        self::trigger('private-order.' . $order['id'], 'status-changed', [
            'order_id'   => $order['id'],
            'new_status' => $newStatus,
            'label'      => ucfirst(str_replace('_', ' ', $newStatus)),
        ]);

        // Also notify admin channel so dashboard can refresh
        self::trigger('private-admin', 'order-status-changed', [
            'order_id'   => $order['id'],
            'new_status' => $newStatus,
        ]);
    }
}