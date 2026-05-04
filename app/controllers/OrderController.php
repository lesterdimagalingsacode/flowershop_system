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

    public function __construct() {
        $this->orderModel     = new Order();
        $this->orderItemModel = new OrderItem();
        $this->productModel   = new Product();
        $this->cartModel      = new Cart();
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

        // Reload cart after update
        $cart = $this->cartModel->getByUser($userId);

        if ($isAsync) {
            $subtotal  = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
            $delivery  = defined('DELIVERY_FEE') ? DELIVERY_FEE : 0.00;
            $cartCount = $this->cartModel->count($userId);
            $itemTotal = isset($cart[$productId])
                ? $cart[$productId]['price'] * $cart[$productId]['quantity']
                : 0;

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
        $this->cartModel->clear(Session::userId());
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

        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $delivery = defined('DELIVERY_FEE') ? DELIVERY_FEE : 0.00;

        $this->view('shop/checkout', [
            'title'    => 'Checkout',
            'cart'     => $cart,
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total'    => $subtotal + $delivery,
            'user'     => Session::user(),
        ], 'main');
    }

    // ── POST /shop/checkout ───────────────────
    public function checkout(): void {
        $this->requireAuth();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/shop/checkout', 'Invalid request.', 'error');

        $userId = Session::userId();
        $cart   = $this->cartModel->getByUser($userId);

        if (empty($cart)) {
            $this->flashRedirect('/shop/cart', 'Your cart is empty.', 'warning');
            return;
        }

        $address = trim($this->post('delivery_address', ''));
        if (empty($address)) {
            Session::flash('message', 'Please enter a delivery address.', 'error');
            $this->redirect('/shop/checkout');
            return;
        }

        $notes    = trim($this->post('notes', ''));
        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $delivery = defined('DELIVERY_FEE') ? DELIVERY_FEE : 0.00;
        $total    = $subtotal + $delivery;

        // Verify stock for all items
        foreach ($cart as $item) {
            $product = $this->productModel->findById($item['product_id']);
            if (!$product || $product['stock'] < $item['quantity']) {
                Session::flash('message', $item['name'] . ' is no longer available in the requested quantity.', 'error');
                $this->redirect('/shop/cart');
                return;
            }
        }

        // Create order
        $orderId = $this->orderModel->create([
            'user_id'          => $userId,
            'subtotal'         => $subtotal,
            'delivery_fee'     => $delivery,
            'total_amount'     => $total,
            'delivery_address' => $address,
            'notes'            => $notes,
        ]);

        if (!$orderId) {
            $this->flashRedirect('/shop/checkout', 'Order failed. Please try again.', 'error');
            return;
        }

        // Create order items & deduct stock
        $this->orderItemModel->createMany((int)$orderId, array_values($cart));

        foreach ($cart as $item) {
            $product  = $this->productModel->findById($item['product_id']);
            $newStock = max(0, $product['stock'] - $item['quantity']);
            $this->productModel->updateStock($item['product_id'], $newStock);
        }

        // Log initial status
        $this->orderModel->updateStatus(
            (int)$orderId,
            'pending',
            $userId,
            'Order placed by customer'
        );

        // Clear DB cart
        $this->cartModel->clear($userId);

        $order = $this->orderModel->findById((int)$orderId);

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

        $validStatuses = ['pending', 'confirmed', 'processing', 'ready', 'delivered', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            $this->jsonError('Invalid status.');
            return;
        }

        $success = $this->orderModel->updateStatus($orderId, $newStatus, Session::userId(), $notes);

        if ($success) {
            $this->jsonSuccess(null, 'Order status updated.');
        } else {
            $this->jsonError('Failed to update order status.');
        }
    }
}