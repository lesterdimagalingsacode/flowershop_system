<?php
// ─────────────────────────────────────────────
//  app/controllers/PusherController.php
//  Authenticates private Pusher channels.
//  Route: POST /pusher/auth
// ─────────────────────────────────────────────

declare(strict_types=1);

class PusherController extends Controller {

    // ── POST /pusher/auth ─────────────────────
    // Called automatically by Pusher JS SDK when
    // subscribing to any private-* channel.
    // Verifies the user is logged in and is
    // allowed to subscribe to that channel.
    public function auth(): void {
        // Must be logged in
        if (!Session::isLoggedIn()) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $socketId    = $this->post('socket_id', '');
        $channelName = $this->post('channel_name', '');
        $userId      = Session::userId();

        if (!$socketId || !$channelName) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing socket_id or channel_name']);
            return;
        }

        // ── Channel access rules ──────────────
        // private-cart.{userId}   → only that user
        // private-order.{orderId} → only the order owner
        // private-admin           → only staff/admin

        $allowed = false;

        if ($channelName === 'private-cart.' . $userId) {
            // Own cart channel — always allowed
            $allowed = true;

        } elseif (str_starts_with($channelName, 'private-order.')) {
            // Order channel — check user owns this order
            $orderId     = (int) str_replace('private-order.', '', $channelName);
            $orderModel  = new Order();
            $order       = $orderModel->findById($orderId);
            $allowed     = $order && (int)$order['user_id'] === $userId;

        } elseif ($channelName === 'private-admin') {
            // Admin channel — staff and admins only
            $allowed = Session::isStaff();
        }

        if (!$allowed) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        // ── Generate auth signature ───────────
        // Pusher SDK signs the socket_id + channel_name
        // with your app secret to prove the server approved it.
        try {
            $pusher = new \Pusher\Pusher(
                PUSHER_APP_KEY,
                PUSHER_APP_SECRET,
                PUSHER_APP_ID,
                [
                    'cluster' => PUSHER_APP_CLUSTER,
                    'useTLS'  => true,
                ]
            );

            $auth = $pusher->authorizeChannel($channelName, $socketId);

            header('Content-Type: application/json');
            echo $auth;

        } catch (\Throwable $e) {
            error_log('[PusherAuth] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Auth failed']);
        }
    }
}