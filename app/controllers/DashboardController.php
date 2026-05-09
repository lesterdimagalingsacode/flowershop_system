<?php
// ─────────────────────────────────────────────
//  app/controllers/DashboardController.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class DashboardController extends Controller {

    private Order     $orderModel;
    private OrderItem $orderItemModel;
    private Product   $productModel;

    public function __construct() {
        $this->orderModel     = new Order();
        $this->orderItemModel = new OrderItem();
        $this->productModel   = new Product();
    }

    // ── GET /admin/dashboard ──────────────────
    public function index(): void {
        $this->requireStaff();

        $to   = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-29 days')); // last 30 days

        $stats = [
            'revenue'       => $this->orderModel->getRevenueInRange($from, $to),
            'orders'        => $this->orderModel->countInRange($from, $to),
            'customers'     => $this->orderModel->countNewCustomers($from, $to),
            'low_stock'     => count($this->productModel->getLowStock()),
            'total_revenue' => $this->orderModel->getTotalRevenue(),
        ];

        $this->view('admin/dashboard', [
            'title'  => 'Dashboard',
            'stats'  => $stats,
            'from'   => $from,
            'to'     => $to,
        ], 'admin');
    }

    // ── GET /admin/api/sales-chart ────────────
    // Returns daily order counts for the past 30 days
    public function salesChart(): void {
        $this->requireStaff();

        $to   = $this->get('to',   date('Y-m-d'));
        $from = $this->get('from', date('Y-m-d', strtotime('-29 days')));

        $rows = $this->orderModel->getSalesByDay($from, $to);

        // Fill in missing days with 0
        $map = [];
        foreach ($rows as $row) {
            $map[$row['date']] = (int)$row['orders'];
        }

        $labels = [];
        $data   = [];
        $cursor = strtotime($from);
        $end    = strtotime($to);

        while ($cursor <= $end) {
            $d        = date('Y-m-d', $cursor);
            $labels[] = date('M d', $cursor);
            $data[]   = $map[$d] ?? 0;
            $cursor   = strtotime('+1 day', $cursor);
        }

        $this->json(['labels' => $labels, 'data' => $data]);
    }

    // ── GET /admin/api/revenue-chart ──────────
    // Returns daily revenue for the past 30 days
    public function revenueChart(): void {
        $this->requireStaff();

        $to   = $this->get('to',   date('Y-m-d'));
        $from = $this->get('from', date('Y-m-d', strtotime('-29 days')));

        $rows = $this->orderModel->getRevenueByDay($from, $to);

        $map = [];
        foreach ($rows as $row) {
            $map[$row['date']] = (float)$row['revenue'];
        }

        $labels = [];
        $data   = [];
        $cursor = strtotime($from);
        $end    = strtotime($to);

        while ($cursor <= $end) {
            $d        = date('Y-m-d', $cursor);
            $labels[] = date('M d', $cursor);
            $data[]   = $map[$d] ?? 0;
            $cursor   = strtotime('+1 day', $cursor);
        }

        $this->json(['labels' => $labels, 'data' => $data]);
    }

    // ── GET /admin/api/inventory-chart ────────
    // Returns stock levels for all active products
    public function inventoryChart(): void {
        $this->requireStaff();

        // Fixed: adminGetAll() takes 3 params (filters, limit, offset)
        $products = $this->productModel->adminGetAll(
            ['is_active' => 1],
            100,
            0
        );

        $labels = [];
        $data   = [];
        $colors = [];

        foreach ($products as $p) {
            $labels[] = $p['name'];
            $stock    = (int)$p['stock'];
            $data[]   = $stock;
            // Color-code by stock level vs each product's own threshold
            $threshold = (int)($p['low_stock_alert'] ?? 5);
            $colors[]  = $stock === 0         ? '#ef4444'   // red  — out of stock
                       : ($stock <= $threshold ? '#f97316'   // orange — low stock
                       :                         '#22c55e'); // green — ok
        }

        $this->json(['labels' => $labels, 'data' => $data, 'colors' => $colors]);
    }

    // ── GET /admin/api/best-sellers ───────────
    public function bestSellers(): void {
        $this->requireStaff();

        $to   = $this->get('to',   date('Y-m-d'));
        $from = $this->get('from', date('Y-m-d', strtotime('-29 days')));

        $rows   = $this->orderItemModel->getBestSellers($from, $to, 8);
        $labels = array_column($rows, 'name');
        $data   = array_map(fn($r) => (int)$r['total_sold'], $rows);

        $this->json(['labels' => $labels, 'data' => $data]);
    }

    // ── GET /admin/api/status-chart ───────────
    public function statusChart(): void {
        $this->requireStaff();

        $rows   = $this->orderModel->getCountByStatus();
        $labels = array_column($rows, 'status');
        $data   = array_map(fn($r) => (int)$r['total'], $rows);

        $this->json(['labels' => $labels, 'data' => $data]);
    }

}