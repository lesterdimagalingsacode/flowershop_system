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

    // ══════════════════════════════════════════
    //  CUSTOMER ROUTES
    // ══════════════════════════════════════════

    // ── GET /shop ─────────────────────────────
    public function catalog(): void {
        $filters = [
            'search'       => trim($this->get('search', '')),
            'min_price'    => $this->get('min_price', ''),
            'max_price'    => $this->get('max_price', ''),
            'availability' => $this->get('availability', ''),
        ];

        $categories = $this->productModel->getCategories();
        $priceRange = $this->productModel->getPriceRange();

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

        $formatted = array_map(function ($p) {
            return [
                'id'            => $p['id'],
                'name'          => $p['name'],
                'slug'          => $p['slug'],
                'description'   => $p['description'] ?? '',
                'price'         => number_format((float) $p['price'], 2),
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
        $product = $this->productModel->findById((int) ($params['id'] ?? 0));

        if (!$product) {
            $this->jsonError('Product not found.', 404);
            return;
        }

        $this->jsonSuccess([
            'id'            => $product['id'],
            'name'          => $product['name'],
            'slug'          => $product['slug'],
            'description'   => $product['description'] ?? '',
            'price'         => number_format((float) $product['price'], 2),
            'stock'         => (int) $product['stock'],
            'low_stock'     => (int) $product['low_stock_alert'],
            'category_name' => $product['category_name'] ?? '',
            'image_url'     => $product['image']
                ? APP_URL . '/images/products/' . $product['image']
                : 'https://picsum.photos/seed/' . $product['id'] . '/400/300',
        ]);
    }

    // ══════════════════════════════════════════
    //  ADMIN ROUTES
    // ══════════════════════════════════════════

    // ── GET /admin/products ───────────────────
    public function adminIndex(): void {
        $this->requireStaff();

        $filters = [
            'search'      => trim($this->get('search', '')),
            'category_id' => $this->get('category_id', ''),
            'is_active'   => $this->get('is_active', ''),
        ];

        $perPage = 20;
        $page    = max(1, (int) $this->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $products   = $this->productModel->adminGetAll($filters, $perPage, $offset);
        $total      = $this->productModel->adminCountAll($filters);
        $categories = $this->productModel->getCategories();

        $this->view('admin/products', [
            'title'      => 'Products',
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $filters,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ], 'admin');
    }

    // ── GET /admin/products/search (Async Fetch API) ──
    public function adminSearch(): void {
        $this->requireStaff();

        $filters = [
            'search'      => trim($this->get('search', '')),
            'category_id' => $this->get('category_id', ''),
            'is_active'   => $this->get('is_active', ''),
        ];

        $perPage = 20;
        $page    = max(1, (int) $this->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $products = $this->productModel->adminGetAll($filters, $perPage, $offset);
        $total    = $this->productModel->adminCountAll($filters);

        $this->jsonSuccess([
            'products'    => $products,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ]);
    }

    // ── GET /admin/products/create ────────────
    public function create(): void {
        $this->requireStaff();

        $categories = $this->productModel->getCategories();

        $this->view('admin/product-form', [
            'title'      => 'Add Product',
            'categories' => $categories,
            'product'    => null,
            'isEdit'     => false,
        ], 'admin');
    }

    // ── POST /admin/products/create ───────────
    public function store(): void {
        $this->requireStaff();
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''));

        $name        = trim($this->post('name', ''));
        $categoryId  = (int) $this->post('category_id', 0);
        $price       = (float) $this->post('price', 0);
        $stock       = (int) $this->post('stock', 0);
        $lowStock    = (int) $this->post('low_stock_alert', 5);
        $description = trim($this->post('description', ''));
        $isActive    = (int) $this->post('is_active', 0);

        if (!$name || $categoryId <= 0 || $price <= 0) {
            $this->flashRedirect('/admin/products/create', 'Please fill in all required fields.', 'error');
            return;
        }

        $slug = $this->generateSlug($name);
        if ($this->productModel->slugExists($slug)) {
            $slug .= '-' . time();
        }

        $imageName = $this->handleImageUpload();
        if ($imageName === false) {
            $this->flashRedirect('/admin/products/create', 'Image upload failed. Please try again.', 'error');
            return;
        }

        $id = $this->productModel->create([
            'category_id'     => $categoryId,
            'name'            => $name,
            'slug'            => $slug,
            'description'     => $description ?: null,
            'price'           => $price,
            'stock'           => $stock,
            'low_stock_alert' => $lowStock,
            'image'           => $imageName,
            'is_active'       => $isActive,
        ]);

        if ($id) {
            $this->flashRedirect('/admin/products', 'Product "' . $name . '" created successfully.', 'success');
        } else {
            $this->flashRedirect('/admin/products/create', 'Failed to create product. Please try again.', 'error');
        }
    }

    // ── GET /admin/products/{id}/edit ─────────
    public function edit(array $params): void {
        $this->requireStaff();

        $product = $this->productModel->findByIdAdmin((int) ($params['id'] ?? 0));

        if (!$product) {
            $this->flashRedirect('/admin/products', 'Product not found.', 'error');
            return;
        }

        $categories = $this->productModel->getCategories();

        $this->view('admin/product-form', [
            'title'      => 'Edit Product',
            'categories' => $categories,
            'product'    => $product,
            'isEdit'     => true,
        ], 'admin');
    }

    // ── POST /admin/products/{id}/edit ────────
    public function update(array $params): void {
        $this->requireStaff();
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''));

        $id      = (int) ($params['id'] ?? 0);
        $product = $this->productModel->findByIdAdmin($id);

        if (!$product) {
            $this->flashRedirect('/admin/products', 'Product not found.', 'error');
            return;
        }

        $name        = trim($this->post('name', ''));
        $categoryId  = (int) $this->post('category_id', 0);
        $price       = (float) $this->post('price', 0);
        $stock       = (int) $this->post('stock', 0);
        $lowStock    = (int) $this->post('low_stock_alert', 5);
        $description = trim($this->post('description', ''));
        $isActive    = (int) $this->post('is_active', 0);

        if (!$name || $categoryId <= 0 || $price <= 0) {
            $this->flashRedirect("/admin/products/{$id}/edit", 'Please fill in all required fields.', 'error');
            return;
        }

        $slug = $product['slug'];
        if ($name !== $product['name']) {
            $slug = $this->generateSlug($name);
            if ($this->productModel->slugExists($slug, $id)) {
                $slug .= '-' . time();
            }
        }

        $imageName = $product['image'];
        $uploaded  = $this->handleImageUpload();
        if ($uploaded === false) {
            $this->flashRedirect("/admin/products/{$id}/edit", 'Image upload failed.', 'error');
            return;
        }
        if ($uploaded !== null) {
            if ($imageName) {
                $oldPath = BASE_PATH . '/public/images/products/' . $imageName;
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $imageName = $uploaded;
        }

        $this->productModel->update($id, [
            'category_id'     => $categoryId,
            'name'            => $name,
            'slug'            => $slug,
            'description'     => $description ?: null,
            'price'           => $price,
            'stock'           => $stock,
            'low_stock_alert' => $lowStock,
            'image'           => $imageName,
            'is_active'       => $isActive,
        ]);

        $this->flashRedirect('/admin/products', 'Product updated successfully.', 'success');
    }

    // ── POST /admin/products/{id}/delete ──────
    public function destroy(array $params): void {
        $this->requireStaff();
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''));

        $id      = (int) ($params['id'] ?? 0);
        $product = $this->productModel->findByIdAdmin($id);

        if (!$product) {
            $this->flashRedirect('/admin/products', 'Product not found.', 'error');
            return;
        }

        // Delete image file if exists
        if ($product['image']) {
            $imgPath = BASE_PATH . '/public/images/products/' . $product['image'];
            if (file_exists($imgPath)) {
                @unlink($imgPath);
            }
        }

        $this->productModel->delete($id);
        $this->flashRedirect('/admin/products', 'Product "' . $product['name'] . '" deleted.', 'success');
    }

    // ══════════════════════════════════════════
    //  INVENTORY
    // ══════════════════════════════════════════

    // ── GET /admin/inventory ──────────────────
    public function inventory(): void {
        $this->requireStaff();

        $filters = [
            'search'      => trim($this->get('search', '')),
            'category_id' => $this->get('category_id', ''),
        ];

        $products   = $this->productModel->adminGetAll($filters, 100, 0);
        $categories = $this->productModel->getCategories();

        $this->view('admin/inventory', [
            'title'      => 'Inventory',
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $filters,
        ], 'admin');
    }

    // ── POST /admin/inventory/{id}/stock ──────
    public function updateStock(array $params): void {
        $this->requireStaff();
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''));

        $id       = (int) ($params['id'] ?? 0);
        $quantity = (int) $this->post('stock', 0);

        if ($quantity < 0) {
            $this->jsonError('Stock cannot be negative.');
            return;
        }

        $product = $this->productModel->findByIdAdmin($id);
        if (!$product) {
            $this->jsonError('Product not found.', 404);
            return;
        }

        $this->productModel->updateStock($id, $quantity);
        $this->jsonSuccess(['stock' => $quantity], 'Stock updated.');
    }

    // ══════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════

    private function generateSlug(string $name): string {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Handle image upload — auto-compress to WebP, resize to 800x800 max.
     * Returns filename string on success, null if no file, false on error.
     */
    private function handleImageUpload(): string|null|false {
        if (empty($_FILES['image']['name'])) {
            return null;
        }

        $file    = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 3 * 1024 * 1024;

        if ($file['error'] !== UPLOAD_ERR_OK)         return false;
        if (!in_array($file['type'], $allowed, true))  return false;
        if ($file['size'] > $maxSize)                  return false;

        // Create image resource from upload
        $source = match($file['type']) {
            'image/jpeg' => imagecreatefromjpeg($file['tmp_name']),
            'image/png'  => imagecreatefrompng($file['tmp_name']),
            'image/webp' => imagecreatefromwebp($file['tmp_name']),
            'image/gif'  => imagecreatefromgif($file['tmp_name']),
            default      => false,
        };

        if (!$source) return false;

        // Resize to max 800x800 keeping aspect ratio
        $origW  = imagesx($source);
        $origH  = imagesy($source);
        $maxDim = 800;

        if ($origW > $maxDim || $origH > $maxDim) {
            $ratio   = min($maxDim / $origW, $maxDim / $origH);
            $newW    = (int)($origW * $ratio);
            $newH    = (int)($origH * $ratio);
            $resized = imagecreatetruecolor($newW, $newH);

            // Preserve transparency
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $newW, $newH, $transparent);

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($source);
            $source = $resized;
        }

        // Ensure upload directory exists
        $uploadDir = BASE_PATH . '/public/images/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Save as WebP at quality 80
        $filename = uniqid('product_', true) . '.webp';
        $dest     = $uploadDir . $filename;

        if (!imagewebp($source, $dest, 80)) {
            imagedestroy($source);
            return false;
        }

        imagedestroy($source);
        return $filename;
    }
}