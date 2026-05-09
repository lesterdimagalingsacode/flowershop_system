<?php
// ─────────────────────────────────────────────
//  app/controllers/OrderController.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class OrderController extends Controller {

    private Order     $orderModel;
    private OrderItem $orderItemModel;
    private Product   $productModel;
    private Cart      $cartModel;
    private PromoCode $promoModel;

    public function __construct() {
        $this->orderModel     = new Order();
        $this->orderItemModel = new OrderItem();
        $this->productModel   = new Product();
        $this->cartModel      = new Cart();
        $this->promoModel     = new PromoCode();
    }

    // ══════════════════════════════════════════
    //  CART
    // ══════════════════════════════════════════

    // ── GET /shop/cart ────────────────────────
    public function cart(): void {
        $this->requireAuth();

        $userId   = Session::userId();
        $cart     = $this->cartModel->getByUser($userId);
        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $delivery = defined('DELIVERY_FEE') ? DELIVERY_FEE : 0.00;

        $this->view('shop/cart', [
            'title'    => 'My Cart',
            'cart'     => $cart,
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total'    => $subtotal + $delivery,
        ], 'main');
    }

    // ── POST /shop/cart/add ───────────────────
    public function addToCart(): void {
        $this->requireAuth();

        $productId = (int) $this->post('product_id', 0);
        $quantity  = max(1, (int) $this->post('quantity', 1));
        $isAsync   = $this->isAjax();
        $userId    = Session::userId();

        if (!$productId) {
            $isAsync ? $this->jsonError('Invalid product.') : $this->redirectBack();
            return;
        }

        $product = $this->productModel->findById($productId);

        if (!$product) {
            $isAsync ? $this->jsonError('Product not found.') : $this->redirectBack();
            return;
        }

        if ($product['stock'] <= 0) {
            $isAsync ? $this->jsonError('Sorry, this product is out of stock.') : $this->redirectBack();
            return;
        }

        $cart       = $this->cartModel->getByUser($userId);
        $currentQty = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;

        if ($currentQty + $quantity > $product['stock']) {
            $isAsync ? $this->jsonError('Not enough stock available.') : $this->redirectBack();
            return;
        }

        $this->cartModel->addOrUpdate($userId, $productId, $quantity);

        $cartCount = $this->cartModel->count($userId);

        // ── Pusher: notify all devices of this user ──
        PusherService::cartUpdated($userId, $cartCount);

        if ($isAsync) {
            $this->jsonSuccess(['cart_count' => $cartCount], $product['name'] . ' added to cart!');
            return;
        }

        Session::flash('message', $product['name'] . ' added to cart!', 'success');
        $this->redirectBack();
    }

    // ── POST /shop/cart/update ────────────────
    public function updateCart(): void {
        $this->requireAuth();

        $productId = (int) $this->post('product_id', 0);
        $quantity  = (int) $this->post('quantity', 1);
        $isAsync   = $this->isAjax();
        $userId    = Session::userId();

        $cart = $this->cartModel->getByUser($userId);

        if (!isset($cart[$productId])) {
            $isAsync ? $this->jsonError('Item not in cart.') : $this->redirectBack();
            return;
        }

        if ($quantity <= 0) {
            $this->cartModel->remove($userId, $productId);
        } else {
            $product = $this->productModel->findById($productId);
            $maxQty  = $product ? $product['stock'] : 99;
            $this->cartModel->update($userId, $productId, min($quantity, $maxQty));
        }

        $cart = $this->cartModel->getByUser($userId);

        $subtotal  = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $delivery  = defined('DELIVERY_FEE') ? DELIVERY_FEE : 0.00;
        $cartCount = $this->cartModel->count($userId);
        $itemTotal = isset($cart[$productId])
            ? $cart[$productId]['price'] * $cart[$productId]['quantity']
            : 0;

        // ── Pusher: sync cart count across devices ──
        PusherService::cartUpdated($userId, $cartCount);

        if ($isAsync) {
            $this->jsonSuccess([
                'cart_count' => $cartCount,
                'item_total' => $itemTotal,
                'subtotal'   => $subtotal,
                'total'      => $subtotal + $delivery,
                'removed'    => $quantity <= 0,
                'empty'      => empty($cart),
            ]);
            return;
        }

        $this->redirectBack();
    }

    // ── POST /shop/cart/remove ────────────────
    public function removeFromCart(): void {
        $this->requireAuth();

        $productId = (int) $this->post('product_id', 0);
        $isAsync   = $this->isAjax();
        $userId    = Session::userId();

        $this->cartModel->remove($userId, $productId);

        $cart      = $this->cartModel->getByUser($userId);
        $subtotal  = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $delivery  = defined('DELIVERY_FEE') ? DELIVERY_FEE : 0.00;
        $cartCount = $this->cartModel->count($userId);

        // ── Pusher: sync cart count across devices ──
        PusherService::cartUpdated($userId, $cartCount);

        if ($isAsync) {
            $this->jsonSuccess([
                'cart_count' => $cartCount,
                'subtotal'   => $subtotal,
                'total'      => $subtotal + $delivery,
                'empty'      => empty($cart),
            ], 'Item removed from cart.');
            return;
        }

        Session::flash('message', 'Item removed from cart.', 'info');
        $this->redirectBack();
    }

    // ── POST /shop/cart/clear ─────────────────
    public function clearCart(): void {
        $this->requireAuth();
        $userId = Session::userId();
        $this->cartModel->clear($userId);

        // ── Pusher: cart cleared ──
        PusherService::cartUpdated($userId, 0);

        Session::flash('message', 'Cart cleared.', 'info');
        $this->redirect('/shop/cart');
    }

    // ══════════════════════════════════════════
    //  CHECKOUT
    // ══════════════════════════════════════════

    // ── GET /shop/checkout ────────────────────
    public function checkoutForm(): void {
        $this->requireAuth();

        $userId = Session::userId();
        $cart   = $this->cartModel->getByUser($userId);

        if (empty($cart)) {
            Session::flash('message', 'Your cart is empty.', 'warning');
            $this->redirect('/shop/cart');
            return;
        }

        $subtotal    = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $delivery    = defined('DELIVERY_FEE') ? DELIVERY_FEE : 0.00;
        $selectedIds = array_keys($cart);

        $this->view('shop/checkout', [
            'title'       => 'Checkout',
            'cart'        => $cart,
            'subtotal'    => $subtotal,
            'delivery'    => $delivery,
            'total'       => $subtotal + $delivery,
            'user'        => Session::user(),
            'selectedIds' => $selectedIds,
        ], 'main');
    }

    // ── POST /shop/checkout ───────────────────
    public function checkout(): void {
        $this->requireAuth();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/shop/checkout', 'Invalid request.', 'error');

        $userId   = Session::userId();
        $fullCart = $this->cartModel->getByUser($userId);

        if (empty($fullCart)) {
            $this->flashRedirect('/shop/cart', 'Your cart is empty.', 'warning');
            return;
        }

        $rawIds      = $this->post('selected_items', []);
        $selectedIds = array_map('intval', is_array($rawIds) ? $rawIds : explode(',', (string)$rawIds));
        $selectedIds = array_values(array_filter($selectedIds));

        if (empty($selectedIds)) {
            Session::flash('message', 'Please select at least one item to checkout.', 'warning');
            $this->redirect('/shop/cart');
            return;
        }

        $selectedLookup = array_fill_keys($selectedIds, true);
        $cart           = array_intersect_key($fullCart, $selectedLookup);

        if (empty($cart)) {
            Session::flash('message', 'Selected items are no longer in your cart.', 'warning');
            $this->redirect('/shop/cart');
            return;
        }

        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $delivery = defined('DELIVERY_FEE') ? DELIVERY_FEE : 0.00;

        // ── Payment method ────────────────────
        $paymentMethod = $this->post('payment_method', 'cod');
        if (!in_array($paymentMethod, ['cod', 'online'])) {
            $paymentMethod = 'cod';
        }

        // ── Promo code ────────────────────────
        $promoCodeInput = trim($this->post('promo_code', ''));
        $discountAmount = 0.00;
        $appliedCode    = null;
        $appliedPromo   = null;

        if (!empty($promoCodeInput)) {
            $promoResult = $this->promoModel->validate($promoCodeInput, $subtotal);

            if ($promoResult['error']) {
                $address = trim($this->post('delivery_address', ''));
                Session::flash('message', $promoResult['error'], 'error');
                $this->view('shop/checkout', [
                    'title'           => 'Checkout',
                    'cart'            => $cart,
                    'subtotal'        => $subtotal,
                    'delivery'        => $delivery,
                    'total'           => $subtotal + $delivery,
                    'user'            => Session::user(),
                    'selectedIds'     => $selectedIds,
                    'promo_code'      => $promoCodeInput,
                    'discount_amount' => 0,
                ], 'main');
                return;
            }

            $discountAmount = (float)$promoResult['discount'];
            $appliedCode    = strtoupper($promoCodeInput);
            $appliedPromo   = $promoResult['promo'];
        }

        $total = max(0, $subtotal + $delivery - $discountAmount);

        // ── Delivery address ──────────────────
        $address = trim($this->post('delivery_address', ''));
        if (empty($address)) {
            Session::flash('message', 'Please enter a delivery address.', 'error');
            $this->view('shop/checkout', [
                'title'           => 'Checkout',
                'cart'            => $cart,
                'subtotal'        => $subtotal,
                'delivery'        => $delivery,
                'total'           => $total,
                'user'            => Session::user(),
                'selectedIds'     => $selectedIds,
                'promo_code'      => $promoCodeInput,
                'discount_amount' => $discountAmount,
            ], 'main');
            return;
        }

        $notes = trim($this->post('notes', ''));

        // ── Stock verification ────────────────
        foreach ($cart as $item) {
            $product = $this->productModel->findById($item['product_id']);
            if (!$product || $product['stock'] < $item['quantity']) {
                Session::flash('message', $item['name'] . ' is no longer available in the requested quantity.', 'error');
                $this->redirect('/shop/cart');
                return;
            }
        }

        // ── Create order ──────────────────────
        $orderId = $this->orderModel->create([
            'user_id'          => $userId,
            'subtotal'         => $subtotal,
            'delivery_fee'     => $delivery,
            'discount_amount'  => $discountAmount,
            'total_amount'     => $total,
            'delivery_address' => $address,
            'notes'            => $notes,
            'promo_code'       => $appliedCode,
            'payment_method'   => $paymentMethod,  // ← new
        ]);

        if (!$orderId) {
            $this->flashRedirect('/shop/checkout', 'Order failed. Please try again.', 'error');
            return;
        }

        // ── Order items & stock deduction ─────
        $this->orderItemModel->createMany((int)$orderId, array_values($cart));

        foreach ($cart as $item) {
            $product  = $this->productModel->findById($item['product_id']);
            $newStock = max(0, $product['stock'] - $item['quantity']);
            $this->productModel->updateStock($item['product_id'], $newStock);
        }

        // ── Increment promo usage ─────────────
        if ($appliedPromo) {
            $this->promoModel->incrementUses((int)$appliedPromo['id']);
        }

        // ── Log initial status ────────────────
        $this->orderModel->updateStatus(
            (int)$orderId,
            'pending',
            $userId,
            'Order placed by customer' . ($appliedCode ? " (promo: {$appliedCode})" : '')
        );

        // ── Remove checked-out items from cart ─
        foreach ($selectedIds as $productId) {
            $this->cartModel->remove($userId, $productId);
        }

        // ── Pusher: update cart count (now lower) ──
        $newCartCount = $this->cartModel->count($userId);
        PusherService::cartUpdated($userId, $newCartCount);

        $order = $this->orderModel->findById((int)$orderId);

        // ── Online payment → redirect to PayMongo ──
        if ($paymentMethod === 'online') {
            $checkoutUrl = PaymentController::createCheckoutSession($order, array_values($cart));

            if ($checkoutUrl) {
                // Don't notify admin yet — wait for payment confirmation in PaymentController::success()
                header('Location: ' . $checkoutUrl);
                exit;
            }

            // PayMongo session creation failed — fall back to COD flow with error
            Session::flash('message', 'Could not connect to payment gateway. Please try again or choose Cash on Delivery.', 'error');
            $this->redirect('/orders/' . $orderId);
            return;
        }

        // ── COD flow ──────────────────────────
        // Notify admin of new order
        PusherService::newOrder($order);

        Session::flash('message', 'Order placed successfully! Your order number is ' . $order['order_number'] . '.', 'success');
        $this->redirect('/orders/' . $orderId);
    }

    // ══════════════════════════════════════════
    //  CUSTOMER ORDERS
    // ══════════════════════════════════════════

    // ── GET /orders ───────────────────────────
    public function myOrders(): void {
        $this->requireAuth();

        $userId  = Session::userId();
        $page    = max(1, (int) $this->get('page', 1));
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $orders     = $this->orderModel->getByUser($userId, $perPage, $offset);
        $total      = $this->orderModel->countByUser($userId);
        $totalPages = (int) ceil($total / $perPage);

        $this->view('orders/index', [
            'title'      => 'My Orders',
            'orders'     => $orders,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $totalPages,
        ], 'main');
    }

    // ── GET /orders/{id} ──────────────────────
    public function show(array $params): void {
        $this->requireAuth();

        $order = $this->orderModel->findById((int)($params['id'] ?? 0));

        if (!$order || (int)$order['user_id'] !== Session::userId()) {
            $this->abort(404);
            return;
        }

        $items   = $this->orderItemModel->getByOrder($order['id']);
        $history = $this->orderModel->getStatusHistory($order['id']);

        $this->view('orders/show', [
            'title'   => 'Order ' . $order['order_number'],
            'order'   => $order,
            'items'   => $items,
            'history' => $history,
        ], 'main');
    }

    // ── POST /orders/{id}/cancel ──────────────
    public function cancel(array $params): void {
        $this->requireAuth();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/orders', 'Invalid request.', 'error');

        $orderId = (int)($params['id'] ?? 0);
        $success = $this->orderModel->cancel($orderId, Session::userId());

        if ($success) {
            Session::flash('message', 'Order cancelled successfully.', 'success');
        } else {
            Session::flash('message', 'Unable to cancel this order.', 'error');
        }

        $this->redirect('/orders/' . $orderId);
    }

    // ══════════════════════════════════════════
    //  ADMIN
    // ══════════════════════════════════════════

    public function adminIndex(): void {
        $this->requireStaff();

        $status  = $this->get('status', '');
        $page    = max(1, (int) $this->get('page', 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $orders     = $this->orderModel->getAll($perPage, $offset, $status);
        $total      = $this->orderModel->countAll($status);
        $totalPages = (int) ceil($total / $perPage);

        $this->view('admin/orders', [
            'title'      => 'Orders',
            'orders'     => $orders,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $totalPages,
            'status'     => $status,
        ], 'admin');
    }

    public function adminShow(array $params): void {
        $this->requireStaff();

        $order = $this->orderModel->findById((int)($params['id'] ?? 0));
        if (!$order) {
            $this->abort(404);
            return;
        }

        $items   = $this->orderItemModel->getByOrder($order['id']);
        $history = $this->orderModel->getStatusHistory($order['id']);

        $this->view('admin/order-detail', [
            'title'   => 'Order ' . $order['order_number'],
            'order'   => $order,
            'items'   => $items,
            'history' => $history,
        ], 'admin');
    }

    public function updateStatus(array $params): void {
        $this->requireStaff();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->jsonError('Invalid request.', 403);

        $orderId   = (int)($params['id'] ?? 0);
        $newStatus = $this->post('status', '');
        $notes     = $this->post('notes', '');

        $order = $this->orderModel->findById($orderId);
        if (!$order) {
            $this->jsonError('Order not found.');
            return;
        }

        // Define allowed next steps for each status
        $transitions = [
            'pending'    => ['confirmed', 'cancelled'],
            'confirmed'  => ['processing', 'cancelled'],
            'processing' => ['ready', 'cancelled'],
            'ready'      => ['delivered'],
            'delivered'  => [],
            'cancelled'  => [],
        ];

        $current = $order['status'];
        $allowed = $transitions[$current] ?? [];

        if (!in_array($newStatus, $allowed)) {
            $this->jsonError("Cannot move from '{$current}' to '{$newStatus}'.");
            return;
        }

        $success = $this->orderModel->updateStatus($orderId, $newStatus, Session::userId(), $notes);

        if ($success) {
            // ── Pusher: notify customer + admin of status change ──
            PusherService::orderStatusChanged($order, $newStatus);

            $this->jsonSuccess(['new_status' => $newStatus], 'Order status updated.');
        } else {
            $this->jsonError('Failed to update order status.');
        }
    }
}