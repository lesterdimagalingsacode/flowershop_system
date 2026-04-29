<?php
declare(strict_types=1);

class DashboardController extends Controller {
    public function index(): void {
        $this->requireStaff();
        echo "<h1>Dashboard coming soon!</h1>";
    }

    public function salesChart(): void {}
    public function inventoryChart(): void {}
    public function revenueChart(): void {}
}