<?php
// ─────────────────────────────────────────────
//  app/controllers/PaymentController.php
//  Handles PayMongo Checkout Session flow
// ─────────────────────────────────────────────

declare(strict_types=1);

class PaymentController extends Controller {

    private Order $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    // ── Create PayMongo Checkout Session ──────
    public static function createCheckoutSession(array $order, array $items): string|false {
        $secretKey = base64_encode(PAYMONGO_SECRET_KEY . ':');

        $lineItems = [];
        foreach ($items as $item) {
            $lineItems[] = [
                'currency' => 'PHP',
                'amount'   => (int) round($item['price'] * 100),
                'name'     => $item['name'],
                'quantity' => (int) $item['quantity'],
            ];
        }

        if ((float)$order['delivery_fee'] > 0) {
            $lineItems[] = [
                'currency' => 'PHP',
                'amount'   => (int) round($order['delivery_fee'] * 100),
                'name'     => 'Delivery Fee',
                'quantity' => 1,
            ];
        }

        if ((float)($order['discount_amount'] ?? 0) > 0) {
            $lineItems[] = [
                'currency' => 'PHP',
                'amount'   => -(int) round($order['discount_amount'] * 100),
                'name'     => 'Promo Discount',
                'quantity' => 1,
            ];
        }

        // ── Pre-fill customer info on PayMongo page ──
        // Parse delivery address into components
        // Expected format: "Street, City, Province" or full address string
        $addressParts = array_map('trim', explode(',', $order['delivery_address'] ?? ''));

        // Full address in line1, extract city and state from known positions
        // Format: street, barangay, municipality, province, region
        $totalParts   = count($addressParts);
        $province     = $totalParts >= 4 ? $addressParts[$totalParts - 2] : '';
        $municipality = $totalParts >= 3 ? $addressParts[$totalParts - 3] : '';

        // Remove region (last part) — PayMongo doesn't need it
        // Put everything except province and region in line1
        $line1Parts   = array_slice($addressParts, 0, $totalParts - 2);
        $addressLine1 = implode(', ', $line1Parts);

        $billingInfo = [
            'name'    => trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')),
            'email'   => $order['email']   ?? '',
            'phone'   => $order['phone']   ?? '',
            'address' => [
                'line1'       => $addressLine1,   // "Purok 4, Zabali, Baler"
                'city'        => $municipality,   // "Baler"
                'state'       => $province,       // "Aurora"
                'postal_code' => '',
                'country'     => 'PH',
            ],
        ];

        $payload = [
            'data' => [
                'attributes' => [
                    'line_items'           => $lineItems,
                    'payment_method_types' => ['card', 'qrph'],
                    'success_url'          => APP_URL . '/payment/success?order=' . urlencode($order['order_number']),
                    'cancel_url'           => APP_URL . '/payment/cancel?order='  . urlencode($order['order_number']),
                    'description'          => 'Petal & Soul Order ' . $order['order_number'],
                    'billing'              => $billingInfo,
                    'metadata'             => [
                        'order_id'     => (string) $order['id'],
                        'order_number' => $order['order_number'],
                    ],
                ],
            ],
        ];

        $idempotencyKey = 'order_' . $order['id'] . '_' . time();

        $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . $secretKey,
                'Idempotency-Key: ' . $idempotencyKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 && $httpCode !== 201) {
            Logger::error('PayMongo createCheckoutSession failed: ' . $response);
            return false;
        }

        $data        = json_decode($response, true);
        $checkoutUrl = $data['data']['attributes']['checkout_url'] ?? false;

        if ($checkoutUrl) {
            $sessionId = $data['data']['id'];
            $db = Database::getInstance();
            $db->execute(
                "INSERT INTO payments (order_id, paymongo_link_id, idempotency_key, amount, currency, status, payment_method)
                 VALUES (?, ?, ?, ?, 'PHP', 'pending', 'online')
                 ON DUPLICATE KEY UPDATE paymongo_link_id = VALUES(paymongo_link_id)",
                [$order['id'], $sessionId, $idempotencyKey, $order['total_amount']]
            );
        }

        return $checkoutUrl;
    }

    // ── Payment Success ───────────────────────
    public function success(): void {
        $orderNumber = $_GET['order'] ?? '';

        if (!$orderNumber) {
            $this->redirect('/shop');
            return;
        }

        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order) {
            $this->redirect('/shop');
            return;
        }

        $payment       = $this->orderModel->getPayment((int)$order['id']);
        $verified      = false;
        $paymentMethod = 'online';

        if ($payment && $payment['paymongo_link_id']) {
            $verified = $this->verifyCheckoutSession(
                $payment['paymongo_link_id'],
                (int)$order['id'],
                $paymentMethod
            );
        }

        // Re-fetch after status update
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if ($verified && class_exists('PusherService')) {
            PusherService::newOrder($order);
            PusherService::orderStatusChanged($order, 'confirmed');
        }

        $this->view('payment/success', [
            'title'    => 'Payment Successful',
            'order'    => $order,
            'verified' => $verified,
        ], 'main');
    }

    // ── Payment Failed ────────────────────────
    public function failed(): void {
        $orderNumber = $_GET['order'] ?? '';
        $order       = $orderNumber ? $this->orderModel->findByOrderNumber($orderNumber) : null;

        $this->view('payment/failed', [
            'title'     => 'Payment Failed',
            'order'     => $order,
            'cancelled' => false,
        ], 'main');
    }

    // ── Payment Cancel ────────────────────────
    public function cancel(): void {
        $orderNumber = $_GET['order'] ?? '';
        $order       = $orderNumber ? $this->orderModel->findByOrderNumber($orderNumber) : null;

        $this->view('payment/failed', [
            'title'     => 'Payment Cancelled',
            'order'     => $order,
            'cancelled' => true,
        ], 'main');
    }

    // ── Verify Checkout Session ───────────────
    private function verifyCheckoutSession(string $sessionId, int $orderId, string &$paymentMethod): bool {
        $secretKey = base64_encode(PAYMONGO_SECRET_KEY . ':');

        $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions/' . $sessionId);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Authorization: Basic ' . $secretKey,
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Logger::error('PayMongo verifyCheckoutSession failed: ' . $response);
            return false;
        }

        $data       = json_decode($response, true);
        $attributes = $data['data']['attributes'] ?? [];
        $status     = $attributes['payment_intent']['attributes']['status'] ?? '';
        $payments   = $attributes['payments'] ?? [];

        if ($status !== 'succeeded') {
            return false;
        }

        if (!empty($payments)) {
            $paymentMethod = $payments[0]['attributes']['source']['type'] ?? 'online';
        }

        $paymongoPaymentId = $payments[0]['id'] ?? null;
        $paidAt            = date('Y-m-d H:i:s');

        // Idempotency — don't process twice
        $db = Database::getInstance();
        $existing = $db->queryOne(
            "SELECT status FROM payments WHERE order_id = ? AND paymongo_link_id = ?",
            [$orderId, $sessionId]
        );

        if ($existing && $existing['status'] === 'paid') {
            return true;
        }

        $db->execute(
            "UPDATE payments
             SET status = 'paid', payment_method = ?, paymongo_payment_id = ?, paid_at = ?
             WHERE order_id = ? AND paymongo_link_id = ?",
            [$paymentMethod, $paymongoPaymentId, $paidAt, $orderId, $sessionId]
        );

        $orderModel = new Order();
        $order      = $orderModel->findById($orderId);
        $userId     = (int)($order['user_id'] ?? 0);

        $orderModel->updateStatus(
            $orderId,
            'confirmed',
            $userId,
            'Payment confirmed via PayMongo (' . $paymentMethod . ')'
        );

        return true;
    }
}