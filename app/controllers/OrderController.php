<?php
declare(strict_types=1);

class OrderController extends Controller {
    public function cart(): void { echo "<h1>Cart coming soon!</h1>"; }
    public function addToCart(): void {}
    public function updateCart(): void {}
    public function removeFromCart(): void {}
    public function clearCart(): void {}
    public function checkoutForm(): void {}
    public function checkout(): void {}
    public function myOrders(): void { echo "<h1>Orders coming soon!</h1>"; }
    public function show(array $params): void {}
    public function cancel(array $params): void {}
    public function adminIndex(): void {}
    public function adminShow(array $params): void {}
    public function updateStatus(array $params): void {}
}