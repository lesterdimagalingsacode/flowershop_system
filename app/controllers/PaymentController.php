<?php
// ─────────────────────────────────────────────
//  app/controllers/PaymentController.php
//  Handles PayMongo Payment Intent + Checkout Session flow
// ─────────────────────────────────────────────

declare(strict_types=1);

class PaymentController extends Controller {

    private Order $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    // ── Create PayMongo Checkout Session ──────
    public static function createCheckoutSession(array $order, array $items): string|false
{
    $secretKey = base64_encode(PAYMONGO_SECRET_KEY . ':');

    // ── Build line items (no negative amounts) ──
    $lineItems      = [];
    $itemsTotal     = 0;

    foreach ($items as $item) {
        $unitPrice  = (float)($item['unit_price'] ?? $item['price'] ?? 0);
        $quantity   = (int)$item['quantity'];
        $amount     = (int)round($unitPrice * 100);
        $itemsTotal += $amount * $quantity;

        $lineItems[] = [
            'currency' => 'PHP',
            'amount'   => $amount,
            'name'     => $item['name'],
            'quantity' => $quantity,
        ];
    }

    // ── Apply discount by reducing the first item's effective amount ──
    $discountCents = (int)round((float)($order['discount_amount'] ?? 0) * 100);
    if ($discountCents > 0 && !empty($lineItems)) {
        $lineItems[0]['amount'] = max(1, $lineItems[0]['amount'] - (int)ceil($discountCents / $lineItems[0]['quantity']));
    }

    // ── Delivery fee ──
    if ((float)($order['delivery_fee'] ?? 0) > 0) {
        $lineItems[] = [
            'currency' => 'PHP',
            'amount'   => (int)round($order['delivery_fee'] * 100),
            'name'     => 'Delivery Fee',
            'quantity' => 1,
        ];
    }

    // ── Billing info ──
    $addressParts = array_map('trim', explode(',', $order['delivery_address'] ?? ''));
    $totalParts   = count($addressParts);
    $province     = $totalParts >= 4 ? $addressParts[$totalParts - 2] : '';
    $municipality = $totalParts >= 3 ? $addressParts[$totalParts - 3] : '';
    $line1Parts   = array_slice($addressParts, 0, $totalParts - 2);
    $addressLine1 = implode(', ', $line1Parts);

    $billingInfo = [
        'name'    => trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')),
        'email'   => $order['email'] ?? '',
        'phone'   => $order['phone'] ?? '',
        'address' => [
            'line1'       => $addressLine1,
            'city'        => $municipality,
            'state'       => $province,
            'postal_code' => '',
            'country'     => 'PH',
        ],
    ];

    // ── Payload ──
    $payload = [
        'data' => [
            'attributes' => [
                'line_items'           => $lineItems,
                'payment_method_types' => ['card', 'qrph'],
                'success_url'          => APP_URL . '/payment/success?order=' . urlencode($order['order_number']),
                'cancel_url'           => APP_URL . '/payment/failed?order='  . urlencode($order['order_number']),
                'description'          => 'Petal & Soul Order ' . $order['order_number'],
                'billing'              => $billingInfo,
                'metadata'             => [
                    'order_id'     => (string)$order['id'],
                    'order_number' => $order['order_number'],
                ],
            ],
        ],
    ];

    $idempotencyKey = 'order_' . $order['id'] . '_' . time();

    // ── cURL ──
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

    // TEMP DEBUG — writes to a file you can read
    file_put_contents(__DIR__ . '/../../storage/logs/paymongo_debug.txt', 
        "HTTP: $httpCode\nRESPONSE: $response\nPAYLOAD: " . json_encode($payload) . "\n",
        FILE_APPEND
    );

    if ($httpCode !== 200 && $httpCode !== 201) {
        Logger::error('PayMongo createCheckoutSession failed [HTTP ' . $httpCode . ']: ' . $response);
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
             ON DUPLICATE KEY UPDATE paymongo_link_id = VALUES(paymongo_link_id), idempotency_key = VALUES(idempotency_key)",
            [$order['id'], $sessionId, $idempotencyKey, $order['total_amount']]
        );
    }

    return $checkoutUrl;
}

    // ── Create & Attach Payment Intent ────────
    public static function createAndAttachPaymentIntent(array $order, array $items, string $paymentMethodId): array|false {
        $secretKey = base64_encode(PAYMONGO_SECRET_KEY . ':');
        $amount    = (int) round($order['total_amount'] * 100);

        // ── Step 1: Create Payment Intent ─────
        $intentPayload = [
            'data' => [
                'attributes' => [
                    'amount'               => $amount,
                    'currency'             => 'PHP',
                    'payment_method_allowed' => ['card'],   
                    'description'          => 'Petal & Soul Order ' . $order['order_number'],
                    'metadata'             => [
                        'order_id'     => (string) $order['id'],
                        'order_number' => $order['order_number'],
                    ],
                ],
            ],
        ];

        $ch = curl_init('https://api.paymongo.com/v1/payment_intents');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . $secretKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($intentPayload),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 && $httpCode !== 201) {
            Logger::error('PayMongo createPaymentIntent failed: ' . $response);
            return false;
        }

        $intentData = json_decode($response, true);
        $intentId   = $intentData['data']['id'] ?? null;
        $clientKey  = $intentData['data']['attributes']['client_key'] ?? null;

        if (!$intentId) return false;

        // ── Step 2: Attach Payment Method ─────
        $returnUrl = APP_URL . '/payment/success?order=' . urlencode($order['order_number']);

        $attachPayload = [
            'data' => [
                'attributes' => [
                    'payment_method' => $paymentMethodId,
                    'client_key'     => $clientKey,
                    'return_url'     => $returnUrl,
                ],
            ],
        ];

        $ch = curl_init('https://api.paymongo.com/v1/payment_intents/' . $intentId . '/attach');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . $secretKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($attachPayload),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 && $httpCode !== 201) {
            Logger::error('PayMongo attachPaymentMethod failed: ' . $response);
            return false;
        }

        $attachData = json_decode($response, true);
        $status     = $attachData['data']['attributes']['status'] ?? '';
        $nextAction = $attachData['data']['attributes']['next_action'] ?? null;

        // ── Save payment record ───────────────
        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO payments (order_id, paymongo_link_id, idempotency_key, amount, currency, status, payment_method)
             VALUES (?, ?, ?, ?, 'PHP', 'pending', 'online')
             ON DUPLICATE KEY UPDATE paymongo_link_id = VALUES(paymongo_link_id)",
            [$order['id'], $intentId, 'pi_' . $order['id'] . '_' . time(), $order['total_amount']]
        );

        // ── 3DS required ──────────────────────
        if ($status === 'awaiting_next_action' && isset($nextAction['redirect']['url'])) {
            return ['redirect_url' => $nextAction['redirect']['url']];
        }

        // ── Succeeded immediately (no 3DS) ────
        if ($status === 'succeeded') {
            $payments      = $attachData['data']['attributes']['payments'] ?? [];
            $paymongoPayId = $payments[0]['id'] ?? null;
            $payMethod     = $payments[0]['attributes']['source']['type'] ?? 'card';

            $db->execute(
                "UPDATE payments
                 SET status = 'paid', payment_method = ?, paymongo_payment_id = ?, paid_at = ?
                 WHERE order_id = ? AND paymongo_link_id = ?",
                [$payMethod, $paymongoPayId, date('Y-m-d H:i:s'), $order['id'], $intentId]
            );

            $orderModel = new Order();
            $orderModel->updateStatus(
                (int)$order['id'],
                'confirmed',
                (int)$order['user_id'],
                'Payment confirmed via PayMongo Payment Intent (' . $payMethod . ')'
            );

            return ['status' => 'paid'];
        }

        // ── Failed ────────────────────────────
        if ($status === 'awaiting_payment_method' || $status === 'failed') {
            $db->execute(
                "UPDATE payments SET status = 'failed' WHERE order_id = ? AND paymongo_link_id = ?",
                [$order['id'], $intentId]
            );
            return ['status' => 'failed'];
        }

        return false;
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
            // ── Try Payment Intent verify first ──
            $verified = $this->verifyPaymentIntent(
                $payment['paymongo_link_id'],
                (int)$order['id'],
                $paymentMethod
            );

            // ── Fallback to Checkout Session verify ──
            if (!$verified) {
                $verified = $this->verifyCheckoutSession(
                    $payment['paymongo_link_id'],
                    (int)$order['id'],
                    $paymentMethod
                );
            }
        }

        // ── Guard: redirect to failed if payment not verified ──
        if (!$verified) {
            $this->redirect('/payment/failed?order=' . urlencode($orderNumber));
            return;
        }

        // Re-fetch after status update
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (class_exists('PusherService')) {
            PusherService::newOrder($order);
            PusherService::orderStatusChanged($order, 'confirmed');
        }

        $orderItemModel = new OrderItem();
        $items          = $orderItemModel->getByOrder((int)$order['id']);
        OrderController::sendOrderConfirmationEmail($order, $items);

        $this->view('payment/success', [
            'title'    => 'Payment Successful',
            'order'    => $order,
            'verified' => true,
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

    // ── Verify Payment Intent (after 3DS return) ──
    private function verifyPaymentIntent(string $intentId, int $orderId, string &$paymentMethod): bool {
        // Only process if it looks like a Payment Intent ID
        if (!str_starts_with($intentId, 'pi_')) return false;

        $secretKey = base64_encode(PAYMONGO_SECRET_KEY . ':');

        $ch = curl_init('https://api.paymongo.com/v1/payment_intents/' . $intentId);
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

        if ($httpCode !== 200) return false;

        $data     = json_decode($response, true);
        $attrs    = $data['data']['attributes'] ?? [];
        $status   = $attrs['status'] ?? '';
        $payments = $attrs['payments'] ?? [];

        if ($status !== 'succeeded') return false;

        if (!empty($payments)) {
            $paymentMethod = $payments[0]['attributes']['source']['type'] ?? 'card';
        }

        $paymongoPaymentId = $payments[0]['id'] ?? null;
        $paidAt            = date('Y-m-d H:i:s');

        $db = Database::getInstance();
        $existing = $db->queryOne(
            "SELECT status FROM payments WHERE order_id = ? AND paymongo_link_id = ?",
            [$orderId, $intentId]
        );

        if ($existing && $existing['status'] === 'paid') return true;

        $db->execute(
            "UPDATE payments
             SET status = 'paid', payment_method = ?, paymongo_payment_id = ?, paid_at = ?
             WHERE order_id = ? AND paymongo_link_id = ?",
            [$paymentMethod, $paymongoPaymentId, $paidAt, $orderId, $intentId]
        );

        $orderModel = new Order();
        $order      = $orderModel->findById($orderId);
        $userId     = (int)($order['user_id'] ?? 0);

        $orderModel->updateStatus(
            $orderId,
            'confirmed',
            $userId,
            'Payment confirmed via PayMongo Payment Intent (' . $paymentMethod . ')'
        );

        return true;
    }

    // ── Verify Checkout Session ───────────────
    private function verifyCheckoutSession(string $sessionId, int $orderId, string &$paymentMethod): bool {
        if (str_starts_with($sessionId, 'pi_')) return false;

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

        if ($httpCode !== 200) return false;

        $data       = json_decode($response, true);
        $attributes = $data['data']['attributes'] ?? [];
        $status     = $attributes['payment_intent']['attributes']['status'] ?? '';
        $payments   = $attributes['payments'] ?? [];

        if ($status !== 'succeeded') return false;

        if (!empty($payments)) {
            $paymentMethod = $payments[0]['attributes']['source']['type'] ?? 'online';
        }

        $paymongoPaymentId = $payments[0]['id'] ?? null;
        $paidAt            = date('Y-m-d H:i:s');

        $db = Database::getInstance();
        $existing = $db->queryOne(
            "SELECT status FROM payments WHERE order_id = ? AND paymongo_link_id = ?",
            [$orderId, $sessionId]
        );

        if ($existing && $existing['status'] === 'paid') return true;

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

    // ── PayMongo Webhook ──────────────────────
    public function webhook(): void {
        $rawBody  = file_get_contents('php://input');
        $payload  = json_decode($rawBody, true);

        $sigHeader    = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';
        $webhookSecret = PAYMONGO_WEBHOOK_SECRET ?? '';

        if ($webhookSecret && $sigHeader) {
            $parts = [];
            foreach (explode(',', $sigHeader) as $part) {
                [$k, $v] = explode('=', $part, 2);
                $parts[$k] = $v;
            }

            $timestamp    = $parts['t']  ?? '';
            $testSig      = $parts['te'] ?? '';
            $liveSig      = $parts['li'] ?? '';

            $signedPayload = $timestamp . '.' . $rawBody;
            $computedSig   = hash_hmac('sha256', $signedPayload, $webhookSecret);

            if ($computedSig !== $testSig && $computedSig !== $liveSig) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid signature']);
                exit;
            }
        }

        $eventType = $payload['data']['attributes']['type'] ?? '';
        $eventData = $payload['data']['attributes']['data'] ?? [];
        $attributes = $eventData['attributes'] ?? [];

        http_response_code(200);

        switch ($eventType) {
            case 'payment.paid':
            case 'checkout_session.payment.paid':
                $this->handlePaymentPaid($attributes);
                break;
            case 'payment.failed':
                $this->handlePaymentFailed($attributes);
                break;
        }

        echo json_encode(['received' => true]);
        exit;
    }

    // ── Handle payment.paid webhook ───────────
    private function handlePaymentPaid(array $attributes): void {
        $paymongoPaymentId = $attributes['id']                  ?? null;
        $sessionId         = $attributes['payment_intent_id']   ?? null;
        $paidAt            = date('Y-m-d H:i:s');
        $paymentMethod     = $attributes['source']['type']      ?? 'online';

        if (!$paymongoPaymentId) return;

        $db      = Database::getInstance();
        $payment = $db->queryOne(
            "SELECT * FROM payments WHERE paymongo_link_id = ?",
            [$sessionId]
        );

        if (!$payment || $payment['status'] === 'paid') return;

        $db->execute(
            "UPDATE payments
             SET status = 'paid', payment_method = ?, paymongo_payment_id = ?, paid_at = ?
             WHERE paymongo_link_id = ?",
            [$paymentMethod, $paymongoPaymentId, $paidAt, $sessionId]
        );

        $orderModel = new Order();
        $order      = $orderModel->findById((int)$payment['order_id']);
        if (!$order) return;

        $orderModel->updateStatus(
            (int)$payment['order_id'],
            'confirmed',
            (int)($order['user_id'] ?? 0),
            'Payment confirmed via PayMongo webhook (' . $paymentMethod . ')'
        );

        $orderItemModel = new OrderItem();
        $items          = $orderItemModel->getByOrder((int)$payment['order_id']);
        OrderController::sendOrderConfirmationEmail($order, $items);

        if (class_exists('PusherService')) {
            PusherService::newOrder($order);
            PusherService::orderStatusChanged($order, 'confirmed');
        }
    }

    // ── Handle payment.failed webhook ─────────
    private function handlePaymentFailed(array $attributes): void {
        $sessionId = $attributes['payment_intent_id'] ?? null;
        if (!$sessionId) return;

        $db      = Database::getInstance();
        $payment = $db->queryOne(
            "SELECT * FROM payments WHERE paymongo_link_id = ?",
            [$sessionId]
        );

        if (!$payment || $payment['status'] === 'paid') return;

        $db->execute(
            "UPDATE payments SET status = 'failed' WHERE paymongo_link_id = ?",
            [$sessionId]
        );
    }

    // ── Retry Payment ─────────────────────────
   
    // ─────────────────────────────────────────────────────────────────────────────
    //  REPLACEMENT for retry() in app/controllers/PaymentController.php
    //
    //  Drop this method in place of the existing retry() method.
    //  No other changes needed in PaymentController.php.
    // ─────────────────────────────────────────────────────────────────────────────

    // ── Retry Payment (Payment Intent flow) ──────
    public function retry(array $params = []): void {
        $id = (int)($params['id'] ?? 0);

        if (!$id) {
            $this->redirect('/orders');
            return;
        }

        $user = Session::user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $order = $this->orderModel->findById($id);

        if (!$order
            || (int)$order['user_id'] !== (int)$user['id']
            || $order['status'] !== 'pending'
            || $order['payment_method'] !== 'online'
        ) {
            $this->redirect('/orders');
            return;
        }

        $payment = $this->orderModel->getPayment($id);
        if ($payment && $payment['status'] === 'paid') {
            $this->redirect('/orders/' . $id);
            return;
        }

        // ── Expect payment_method_id from the retry modal form ──
        $paymentMethodId = trim($_POST['payment_method_id'] ?? '');

        if (!$paymentMethodId) {
            Session::flash('error', 'No payment method provided. Please enter your card details.');
            $this->redirect('/orders/' . $id);
            return;
        }

        $orderItemModel = new OrderItem();
        $items          = $orderItemModel->getByOrder($id);

        // ── Use Payment Intent (same as main checkout) ──
        $result = self::createAndAttachPaymentIntent($order, $items, $paymentMethodId);

        if (!$result) {
            Session::flash('error', 'Could not process payment. Please try again.');
            $this->redirect('/orders/' . $id);
            return;
        }

        // ── 3DS required → redirect to bank auth page ──
        if (isset($result['redirect_url'])) {
            header('Location: ' . $result['redirect_url']);
            exit;
        }

        // ── Paid immediately (no 3DS) ──
        if (($result['status'] ?? '') === 'paid') {
            $this->redirect('/payment/success?order=' . urlencode($order['order_number']));
            return;
        }

        // ── Failed (declined etc.) ──
        Session::flash('error', 'Your card was declined. Please check your details and try again.');
        $this->redirect('/payment/failed?order=' . urlencode($order['order_number']));
    }
}