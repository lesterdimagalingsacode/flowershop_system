<?php
declare(strict_types=1);

class PaymentController extends Controller {
    public function success(): void { echo "<h1>Payment success!</h1>"; }
    public function failed(): void { echo "<h1>Payment failed!</h1>"; }
    public function webhook(): void {}
}