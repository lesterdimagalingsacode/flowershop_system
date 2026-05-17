<?php
// ─────────────────────────────────────────────
//  app/controllers/PromoController.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class PromoController extends Controller {

    private PromoCode $promoModel;

    public function __construct() {
        $this->promoModel = new PromoCode();
    }

    // ══════════════════════════════════════════
    //  ADMIN ROUTES
    // ══════════════════════════════════════════

    // GET /admin/promos
    public function index(): void {
        $this->requireStaff();

        $page    = max(1, (int)$this->get('page', 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $promos     = $this->promoModel->getAll($perPage, $offset);
        $total      = $this->promoModel->countAll();
        $totalPages = (int)ceil($total / $perPage);

        $this->view('admin/promos', [
            'title'      => 'Promo Codes',
            'promos'     => $promos,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $totalPages,
        ], 'admin');
    }

    // GET /admin/promos/create
    public function create(): void {
        $this->requireStaff();
        $this->view('admin/promo-form', [
            'title' => 'New Promo Code',
            'promo' => null,
        ], 'admin');
    }

    // POST /admin/promos/store
    public function store(): void {
        $this->requireStaff();
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/admin/promos', 'Invalid request.', 'error');

        $errors = $this->validateForm();

        if ($errors) {
            Session::flash('message', implode(' ', $errors), 'error');
            $this->view('admin/promo-form', [
                'title' => 'New Promo Code',
                'promo' => $this->promoFormData(),
            ], 'admin');
            return;
        }

        // Check duplicate code
        $existing = $this->promoModel->findByCode($this->post('code', ''));
        if ($existing) {
            Session::flash('message', 'Promo code already exists.', 'error');
            $this->view('admin/promo-form', [
                'title' => 'New Promo Code',
                'promo' => $this->promoFormData(),
            ], 'admin');
            return;
        }

        $id = $this->promoModel->create($this->promoFormData());

        if ($id) {
            Session::flash('message', 'Promo code created successfully.', 'success');
            $this->redirect('/admin/promos');
        } else {
            Session::flash('message', 'Failed to create promo code.', 'error');
            $this->redirect('/admin/promos/create');
        }
    }

    // GET /admin/promos/{id}/edit
    public function edit(array $params): void {
        $this->requireStaff();

        $promo = $this->promoModel->findById((int)($params['id'] ?? 0));
        if (!$promo) { $this->abort(404); return; }

        $this->view('admin/promo-form', [
            'title' => 'Edit Promo Code',
            'promo' => $promo,
        ], 'admin');
    }

    // POST /admin/promos/{id}/update
    public function update(array $params): void {
        $this->requireStaff();
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/admin/promos', 'Invalid request.', 'error');

        $id    = (int)($params['id'] ?? 0);
        $promo = $this->promoModel->findById($id);
        if (!$promo) { $this->abort(404); return; }

        $errors = $this->validateForm();
        if ($errors) {
            Session::flash('message', implode(' ', $errors), 'error');
            $this->view('admin/promo-form', [
                'title' => 'Edit Promo Code',
                'promo' => array_merge($promo, $this->promoFormData()),
            ], 'admin');
            return;
        }

        // Check duplicate code (exclude self)
        $existing = $this->promoModel->findByCode($this->post('code', ''));
        if ($existing && (int)$existing['id'] !== $id) {
            Session::flash('message', 'Another promo with that code already exists.', 'error');
            $this->view('admin/promo-form', [
                'title' => 'Edit Promo Code',
                'promo' => array_merge($promo, $this->promoFormData()),
            ], 'admin');
            return;
        }

        $ok = $this->promoModel->update($id, $this->promoFormData());

        Session::flash(
            'message',
            $ok ? 'Promo code updated.' : 'Failed to update promo code.',
            $ok ? 'success' : 'error'
        );
        $this->redirect('/admin/promos');
    }

    // POST /admin/promos/{id}/delete
    public function destroy(array $params): void {
        $this->requireStaff();
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->jsonError('Invalid request.', 403);

        $ok = $this->promoModel->delete((int)($params['id'] ?? 0));

        if ($this->isAjax()) {
            $ok ? $this->jsonSuccess(null, 'Promo code deleted.')
                : $this->jsonError('Failed to delete promo code.');
            return;
        }

        Session::flash('message', $ok ? 'Promo code deleted.' : 'Failed to delete.', $ok ? 'success' : 'error');
        $this->redirect('/admin/promos');
    }

    // ══════════════════════════════════════════
    //  PUBLIC AJAX — POST /promo/validate
    // ══════════════════════════════════════════

    
    // ─────────────────────────────────────────────────────────────────────
    //  Add this method to PromoController
    //  Route: POST /promo/validate  (already in routes.php)
    // ─────────────────────────────────────────────────────────────────────

    public function validate(): void
    {
        $this->requireAuth();   // customer must be logged in

        $code     = strtoupper(trim($this->post('code', '')));
        $subtotal = (float) $this->post('subtotal', 0);

        if (!$code) {
            $this->jsonError('Please enter a promo code.');
            return;
        }

        $promoModel = new PromoCode();
        $result     = $promoModel->validate($code, $subtotal);

        if ($result['error']) {
            $this->jsonError($result['error']);
            return;
        }

        $promo = $result['promo'];

        $this->jsonSuccess([
            'code'         => $promo['code'],
            'discount'     => $result['discount'],
            'discount_fmt' => '₱' . number_format($result['discount'], 2),
            'type'         => $promo['type'],
            'value'        => $promo['value'],
        ], 'Promo code applied!');
    }

    // ══════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════

    private function promoFormData(): array {
        return [
            'code'       => strtoupper(trim($this->post('code', ''))),
            'type'       => $this->post('type', 'percent'),
            'value'      => (float)$this->post('value', 0),
            'min_order'  => (float)$this->post('min_order', 0),
            'max_uses'   => $this->post('max_uses', ''),
            'is_active'  => (int)$this->post('is_active', 1),
            'expires_at' => $this->post('expires_at', ''),
        ];
    }

    private function validateForm(): array {
        $errors = [];

        $code  = trim($this->post('code', ''));
        $type  = $this->post('type', '');
        $value = (float)$this->post('value', 0);

        if (empty($code))                        $errors[] = 'Code is required.';
        if (!preg_match('/^[A-Z0-9_\-]{2,50}$/i', $code)) $errors[] = 'Code must be 2-50 alphanumeric characters.';
        if (!in_array($type, ['percent', 'fixed'])) $errors[] = 'Invalid discount type.';
        if ($value <= 0)                         $errors[] = 'Discount value must be greater than 0.';
        if ($type === 'percent' && $value > 100) $errors[] = 'Percent discount cannot exceed 100.';

        return $errors;
    }

    // GET /shop/apply-promo?code=SUMMER20&subtotal=1500.00
    public function applyPromo(): void
    {
        $code     = strtoupper(trim($_GET['code'] ?? ''));
        $subtotal = (float) ($_GET['subtotal'] ?? 0);

        if (!$code) {
            $this->json(['success' => false, 'message' => 'Please enter a promo code.']);
            return;
        }

        $promo = Promo::findActiveByCode($code); // new model method below

        if (!$promo) {
            $this->json(['success' => false, 'message' => 'Invalid or expired promo code.']);
            return;
        }

        if ($promo['min_order'] > 0 && $subtotal < $promo['min_order']) {
            $this->json([
                'success' => false,
                'message' => 'Minimum order of ₱' . number_format($promo['min_order'], 2) . ' required.',
            ]);
            return;
        }

        $discount = $promo['type'] === 'percent'
            ? round($subtotal * ($promo['value'] / 100), 2)
            : min((float) $promo['value'], $subtotal); // fixed can't exceed subtotal

        $this->json([
            'success'      => true,
            'message'      => 'Promo applied!',
            'code'         => $promo['code'],
            'discount'     => $discount,
            'discount_fmt' => '₱' . number_format($discount, 2),
            'type'         => $promo['type'],
            'value'        => $promo['value'],
        ]);
    }
}