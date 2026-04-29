<?php
declare(strict_types=1);

class ProductController extends Controller {
    public function catalog(): void {
        echo "<h1>Shop coming soon!</h1>";
    }
    public function show(array $params): void {}
    public function adminIndex(): void {}
    public function create(): void {}
    public function store(): void {}
    public function edit(array $params): void {}
    public function update(array $params): void {}
    public function destroy(array $params): void {}
    public function inventory(): void {}
    public function updateStock(array $params): void {}
}