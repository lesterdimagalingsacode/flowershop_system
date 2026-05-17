<?php
// ─────────────────────────────────────────────
//  core/PusherService.php — Pusher event broadcaster
// ─────────────────────────────────────────────

declare(strict_types=1);

class PusherService
{
    private static ?\Pusher\Pusher $instance = null;

    /** Queued events to fire after response is sent */
    private static array $queue = [];
    private static bool  $shutdownRegistered = false;

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

    // ── Generic trigger (non-blocking) ───────────────────────────────────────
    // Events are queued and fired after PHP sends the HTTP response to the
    // browser. This means add-to-cart, checkout, etc. are never held up
    // waiting for Pusher's API round-trip over the internet.
    public static function trigger(string $channel, string $event, array $data): void
    {
        // Queue the event
        self::$queue[] = compact('channel', 'event', 'data');

        // Register the shutdown handler only once
        if (!self::$shutdownRegistered) {
            self::$shutdownRegistered = true;

            register_shutdown_function(function () {
                // Flush output to browser first so the user isn't waiting
                if (function_exists('fastcgi_finish_request')) {
                    // FPM: sends response immediately, script keeps running
                    fastcgi_finish_request();
                } else {
                    // Non-FPM: close the connection and continue
                    if (!headers_sent()) {
                        header('Connection: close');
                        header('Content-Encoding: none');
                    }
                    $size = ob_get_length();
                    if ($size !== false) {
                        header('Content-Length: ' . $size);
                    }
                    ob_end_flush();
                    flush();
                }

                // Now fire all queued Pusher events — browser already has its response
                $pusher = self::client();
                if (!$pusher) return;

                foreach (self::$queue as $item) {
                    try {
                        $pusher->trigger($item['channel'], $item['event'], $item['data']);
                    } catch (\Throwable $e) {
                        error_log('[Pusher] Failed to trigger: ' . $e->getMessage());
                    }
                }
            });
        }
    }

    // ── Cart updated (per user) ───────────────
    public static function cartUpdated(int $userId, int $cartCount): void
    {
        self::trigger('private-cart.' . $userId, 'cart-updated', [
            'cart_count' => $cartCount,
        ]);
    }

    // ── New order placed ──────────────────────
    public static function newOrder(array $order): void
    {
        self::trigger('private-admin', 'new-order', [
            'order_id'     => $order['id'],
            'order_number' => $order['order_number'],
            'total_amount' => $order['total_amount'],
            'customer'     => $order['customer_name'] ?? 'Customer',
            'order_status' => $order['status'] ?? 'confirmed',
        ]);
    }

    // ── Order status changed ──────────────────
    public static function orderStatusChanged(array $order, string $newStatus): void
    {
        // Notify customer on order detail page
        self::trigger('private-order.' . $order['id'], 'status-changed', [
            'order_id'   => $order['id'],
            'new_status' => $newStatus,
            'label'      => ucfirst(str_replace('_', ' ', $newStatus)),
        ]);

        // Notify customer on any page
        self::trigger('private-user.' . $order['user_id'], 'order-status-changed', [
            'order_id'     => $order['id'],
            'order_number' => $order['order_number'],
            'new_status'   => $newStatus,
            'label'        => ucfirst(str_replace('_', ' ', $newStatus)),
        ]);

        // Notify admin channel
        self::trigger('private-admin', 'order-status-changed', [
            'order_id'   => $order['id'],
            'new_status' => $newStatus,
        ]);
    }
}