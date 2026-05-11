<?php
declare(strict_types=1);

class LandingController extends Controller {
    public function index(): void {
        $productModel = new Product();
        $featured     = $productModel->getAll(['availability' => 'in_stock'], 4, 0);

        $this->view('landing', [
            'featured' => $featured,
        ], 'main');
    }
}