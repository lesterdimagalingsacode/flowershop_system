<?php
// ─────────────────────────────────────────────
//  app/controllers/ProductController.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class ProductController extends Controller {

    private Product $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    // ── GET /shop ─────────────────────────────
    public function catalog(): void {
        $filters = [
            'search'       => trim($this->get('search', '')),
            'min_price'    => $this->get('min_price', ''),
            'max_price'    => $this->get('max_price', ''),
            'availability' => $this->get('availability', ''),
        ];

        $categories     = $this->productModel->getCategories();
        $priceRange     = $this->productModel->getPriceRange();

        // Load products per category for horizontal sections
        $sections = [];
        foreach ($categories as $cat) {
            $catFilters = array_merge($filters, ['category_id' => $cat['id']]);
            $sections[] = [
                'category' => $cat,
                'products' => $this->productModel->getAll($catFilters, 8, 0),
                'total'    => $this->productModel->countAll($catFilters),
            ];
        }

        $this->view('shop/catalog', [
            'title'      => 'Shop',
            'sections'   => $sections,
            'categories' => $categories,
            'priceRange' => $priceRange,
            'filters'    => $filters,
        ], 'main');
    }

    // ── GET /shop/search (Async Fetch API) ────
    public function search(): void {
        $filters = [
            'search'       => trim($this->get('search', '')),
            'category_id'  => $this->get('category_id', ''),
            'min_price'    => $this->get('min_price', ''),
            'max_price'    => $this->get('max_price', ''),
            'availability' => $this->get('availability', ''),
        ];

        $page    = max(1, (int) $this->get('page', 1));
        $perPage = 12;
        $offset  = ($page - 1) * $perPage;

        $products = $this->productModel->getAll($filters, $perPage, $offset);
        $total    = $this->productModel->countAll($filters);

        // Format products for JSON response
        $formatted = array_map(function($p) {
            return [
                'id'            => $p['id'],
                'name'          => $p['name'],
                'slug'          => $p['slug'],
                'description'   => $p['description'] ?? '',
                'price'         => number_format((float)$p['price'], 2),
                'stock'         => $p['stock'],
                'low_stock'     => $p['low_stock_alert'],
                'category_name' => $p['category_name'] ?? 'Uncategorized',
                'image_url'     => $p['image']
                    ? APP_URL . '/images/products/' . $p['image']
                    : null,
                'url'           => APP_URL . '/shop/' . $p['slug'],
            ];
        }, $products);

        $this->jsonSuccess([
            'products'    => $formatted,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ]);
    }

    // ── GET /shop/{slug} ──────────────────────
    public function show(array $params): void {
        $product = $this->productModel->findBySlug($params['slug'] ?? '');

        if (!$product) {
            $this->abort(404);
            return;
        }

        $this->view('shop/show', [
            'title'   => $product['name'],
            'product' => $product,
        ], 'main');
    }

    // ── GET /shop/product/{id} (JSON for modal) ──
    public function productJson(array $params): void {
        $product = $this->productModel->findById((int)($params['id'] ?? 0));

        if (!$product) {
            $this->jsonError('Product not found.', 404);
            return;
        }

        $this->jsonSuccess([
            'id'            => $product['id'],
            'name'          => $product['name'],
            'slug'          => $product['slug'],
            'description'   => $product['description'] ?? '',
            'price'         => number_format((float)$product['price'], 2),
            'stock'         => (int)$product['stock'],
            'low_stock'     => (int)$product['low_stock_alert'],
            'category_name' => $product['category_name'] ?? '',
            'image_url'     => $product['image']
                ? APP_URL . '/images/products/' . $product['image']
                : 'https://picsum.photos/seed/' . $product['id'] . '/400/300',
        ]);
    }

    // ── Admin stubs ───────────────────────────
    public function adminIndex(): void {}
    public function create(): void {}
    public function store(): void {}
    public function edit(array $params): void {}
    public function update(array $params): void {}
    public function destroy(array $params): void {}
    public function inventory(): void {}
    public function updateStock(array $params): void {}
}