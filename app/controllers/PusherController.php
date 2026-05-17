<?php
// ─────────────────────────────────────────────
//  app/controllers/PusherController.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class PusherController extends Controller {

    public function auth(): void {
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

        $allowed = false;

        if ($channelName === 'private-cart.' . $userId) {
            $allowed = true;

        } elseif ($channelName === 'private-user.' . $userId) {
            // User's own global notification channel
            $allowed = true;

        } elseif (str_starts_with($channelName, 'private-order.')) {
            $orderId    = (int) str_replace('private-order.', '', $channelName);
            $orderModel = new Order();
            $order      = $orderModel->findById($orderId);
            $allowed    = $order && (int)$order['user_id'] === $userId;

        } elseif ($channelName === 'private-admin') {
            $allowed = Session::isStaff();
        }

        if (!$allowed) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

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